<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Domain\Repository\SignRepository;
use function curl_init;
use function curl_setopt_array;
use const CURL_HTTP_VERSION_1_1;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_ENCODING;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HTTP_VERSION;
use const CURLOPT_MAXREDIRS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_TIMEOUT;
use const CURLOPT_URL;
use const CURLOPT_HTTPHEADER;


class WiziSignClient implements SignRepository
{
    private $apikey;
    private $apiBaseUrl;
    private $apiBaseUrlWslash;
    private $idfile;
    private $idAdvProc;
    private $member;
    private $fileobject;

    public function __construct(string $apikey, string $mode)
    {
        $this->setApikey($apikey);
        if ($mode === 'prod') {
            $this->apiBaseUrl = 'https://api.yousign.com/';
            $this->apiBaseUrlWslash = 'https://api.yousign.com';
        } else {
            $this->apiBaseUrl = 'https://staging-api.yousign.com/';
            $this->apiBaseUrlWslash = 'https://staging-api.yousign.com';
        }
    }

    public static function world(): string
    {
        return "Client pour l'api Yousign";
    }

    public function setApikey($apikey): void
    {
        $this->apikey = $apikey;
    }

    /**
     * @return mixed
     */
    public function getApikey()
    {
        return $this->apikey;
    }

    public function setMember($member): void
    {
        $this->member = $member;
    }

    /**
     * @return mixed
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @return mixed
     */
    public function getIdfile()
    {
        return $this->idfile;
    }

    /**
     * @return string
     */
    public function getIdAdvProc(): string
    {
        return $this->idAdvProc;
    }

    public function setIdfile($idfile): void
    {
        $this->idfile = $idfile;
    }

    /**
     * permet de recup le fichier signé sur yousign
     *
     * @param $fileid
     * @param $mode
     *
     * @return bool|string
     */
    public function downloadSignedFile($fileid, string $mode = null): string
    {
        $curl = curl_init();
        if ($mode === 'binary') {
            $urlstr =  $this->apiBaseUrlWslash . $fileid . '/download?alt=media';
        } else {
            $urlstr =  $this->apiBaseUrlWslash . $fileid . '/download';
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $urlstr,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getApikey(),
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 'cURL Error #:' . $err;
        }

        return $response;
    }

    /**
     * @param $post
     * @param $action
     * @param $method
     *
     * @return mixed|string
     */
    public function api_request($post, $action, $method)
    {
        header('Content-Type: application/json'); // Specify the type of data
        $ch = curl_init($this->apiBaseUrl . $action); // Initialise cURL
        $post = json_encode($post); // Encode the data array into a JSON string
        $authorization = 'Authorization: Bearer ' . $this->getApikey(); // Prepare the authorisation token
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization ]); // Inject the token into the header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
        $result = curl_exec($ch); // Execute the cURL statement
        $err = curl_error($ch);
        curl_close($ch); // Close the cURL connection

        if ($err) {
            return 'cURL Error #:' . $err;
        }

        return json_decode($result);

        // Return the received data
    }

    /**
     * @return mixed|string
     */
    public function getUsers()
    {
        return $this->api_request([], 'users', 'GET');
    }

    /**
     * @param $filepath
     *
     * @return $this
     */
    public function newProcedure($filepath)
    {
        $curl = curl_init();

        $data = file_get_contents($filepath);
        $b64Doc = base64_encode($data);

        $names = explode('/', $filepath);
        $filename = $names[count($names) - 1];

        $post = [
            'name' => $filename,
            'content' => $b64Doc,
        ];
        $p = json_encode($post);

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiBaseUrl . 'files',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$p,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getApikey(),
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $rtab = json_decode($response, true);

        $this->idfile = $rtab['id'];

        return $this;
    }

    /**
     * @param $members
     * @param $titresignature
     * @param $description
     *
     * @return bool|string
     */
    public function addMembersOnProcedure($members, $titresignature, $description)
    {
        $post2 = [
            'name' => $titresignature,
            'description' => $description,
            'members'=> $members,
        ];

        $p2 = json_encode($post2, 1);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiBaseUrl . 'procedures',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $p2,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getApikey(),
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return $response;
    }

    /**
     * @param $parameters
     * @param bool $notifmail
     *
     * @return bool|string
     */
    public function AdvancedProcedureCreate(array $parameters, bool $webhook = false, string $webhookMethod = '', string $webhookUrl = '', string $webhookHeader = ''): string
    {
        /*
         *
            {
                "name": "My procedure",
                "description": "Description of my procedure with advanced mode",
                "start" : false
            }
         */
        $conf = [];

        if ($webhook === true) {
            $conf['webhook'] = [
                'procedure.finished' => [
                    [
                        'url' => $webhookUrl,
                        'method' => $webhookMethod,
                        'headers' => ['X-Custom-Header' => $webhookHeader],
                    ],
                ],
                'member.finished' => [
                    [
                        'url' => $webhookUrl,
                        'method' => $webhookMethod,
                        'headers' => ['X-Custom-Header' => $webhookHeader],
                    ],
                ],
                'reminder.executed' => [
                    [
                        'url' => $webhookUrl,
                        'method' => $webhookMethod,
                        'headers' => ['X-Custom-Header' => $webhookHeader],
                    ],
                ],
            ];

            $conf['reminders'] = [
                [
                    'interval' => 7,
                    'limit' => 1,
                    "config" => [
                        "email"=> [
                            "reminder.executed"=> [

                            ]
                        ]
                    ]
                 ]
            ];

            /**    */
            $parameters['config'] = $conf;
        }

        $curl = curl_init();

        $params = json_encode($parameters, 1);

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiBaseUrl . 'procedures',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getApikey(),
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 'cURL Error #:' . $err;
        }

        $rtab = json_decode($response, true);

        $this->idAdvProc = $rtab['id'];

        return $response;
    }

    /**
     * @param $filepath
     * @param $namefile
     *
     * @return bool|string
     */
    public function AdvancedProcedureAddFile($filepathOrFileContent, $namefile, $filecontent = false): string
    {
        /*
         {
            "name": "Name of my signable file.pdf",
            "content": "JVBERi0xLjUKJb/3ov4KNiA [...] VPRgo=",
            "procedure": "/procedures/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX"
}
         */

        if ($filecontent === false) {
            $data = file_get_contents($filepathOrFileContent);
            $b64Doc = base64_encode($data);
        } else {
            $b64Doc = base64_encode($filepathOrFileContent);
        }

        $parameters = [
            'name' => $namefile,
            'content' => $b64Doc,
            'procedure' => $this->idAdvProc,
        ];

        $curl = curl_init();
        $params = json_encode($parameters, 1);

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiBaseUrl . 'files',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getApikey(),
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 'cURL Error #:' . $err;
        }

        $rtab = json_decode($response, true);
        $this->idfile = $rtab['id'];

        return $response;
    }

    /**
     * @param $firstname
     * @param $lastname
     * @param $email
     * @param $phone
     *
     * @return string
     */
    public function AdvancedProcedureAddMember($firstname, $lastname, $email, $phone): string
    {
        /*
             {
                "firstname": "John",
                "lastname": "Doe",
                "email": "john.doe@yousign.fr",
                "phone": "+33612345678",
                "procedure": "/procedures/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX"
            }
         */

        $member = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => $phone,
            'procedure' => $this->idAdvProc,
        ];

        $curl = curl_init();

        $param = json_encode($member, 1);

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiBaseUrl . 'members',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$param,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getApikey(),
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 'cURL Error #:' . $err;
        }

        $rtab = json_decode($response, true);
        if (array_key_exists('violations', $rtab)) {
            throw new \Exception($response);
        }
        $this->member = $rtab['id'];

        return $response;
    }

    /**
     * @param $position
     * @param $page
     * @param $mention
     * @param $mention2
     * @param $reason
     *
     * @return bool|string
     */
    public function AdvancedProcedureFileObject($position, $page, $mention, $mention2, $reason): string
    {
        /*
            {
                "file": "/files/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
                "member": "/members/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
                "position": "230,499,464,589",
                "page": 2,
                "mention": "Read and approved",
                "mention2": "Signed by John Doe",
                "reason": "Signed by John Doe (Yousign)"
            }

         */
        $parameter = [
            'file'=> $this->idfile,
            'member'=> $this->member,
            'position'=> $position,
            'page'=> $page,
            'mention'=> $mention,
            'mention2'=> $mention2,
            'reason'=> $reason,
        ];

        $param = json_encode($parameter, 1);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiBaseUrl . 'file_objects',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $param,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getApikey(),
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (! $err) {
            $rtab = json_decode($response, true);
            $this->fileobject = $rtab['id'];

            return $response;
        }

        echo 'cURL Error #:' . $err;
    }

    /**
     * @return bool|string
     */
    public function AdvancedProcedurePut(): string
    {
        /*
            {
               "start": true
            }
         */

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiBaseUrlWslash . '' . $this->idAdvProc,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS =>"{\n   \"start\": true\n}",
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getApikey(),
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 'cURL Error #:' . $err;
        }

        return $response;
    }

    /**
     * @param array $members
     * @param $mailsubject
     * @param $mailMessage
     * @param array $arrayTo
     *
     * @return bool|string
     */
    public function addMemberWhithMailNotif(array $members = [], string $ProcName = '', string $ProcDesc = '', $mailsubject, $mailMessage, array $arrayTo = ['@creator', '@members'])
    {
        $curl = curl_init();

        /*
         * param 1 an array of members
         [
        {
            "firstname": "John",
            "lastname": "Doe",
            "email": "john.doe@yousign.fr",
            "phone": "+33612345678",
            "fileObjects": [
                {
                    "file": "/files/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
                    "page": 2,
                    "position": "230,499,464,589",
                    "mention": "Read and approved",
                    "mention2": "Signed by John Doe"
                }
            ]
        }
    ]
         */
        $conf = [];

        $conf['email'] =
            [
                'member.started' => [
                    [
                        'subject' => $mailsubject,
                        'message' => 'Hello',
                        'to' => ['@member'],
                    ],
                ],
                'procedure.started' => [
                    [
                        'subject' => $mailsubject,
                        'message' => $mailMessage,
                        'to' => $arrayTo,
                    ],
                ],
            ];

        $body = [
            'name' => $ProcName,
            'description' => $ProcDesc,
            'members' => $members,
            'config' => $conf,

        ];

        $param = json_encode($body, true);

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiBaseUrl . 'procedures',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$param,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getApikey(),
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 'cURL Error #:' . $err;
        }

        return $response;
    }

    /**
     * @param $filepath
     * @param $namefile
     *
     * @return bool|string
     */
    public function AdvancedProcedureAddAttachement($filepath, $namefile)
    {
        /*
            {
                "name": "Name of my attachment.pdf",
                "content": "JVBERi0xLjUKJb/3ov4KICA[...]VPRgo=",
                "procedure": "/procedures/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
                "type": "attachment"
            }
         */

        $data = file_get_contents($filepath);
        $b64Doc = base64_encode($data);

        $parameters = [
            'name' => $namefile,
            'content' => $b64Doc,
            'procedure' => $this->idAdvProc,
            'type'=> 'attachment',
        ];

        $param = json_encode($parameters, true);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiBaseUrl . 'files',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $param,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getApikey(),
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 'cURL Error #:' . $err;
        }

        return $response;
    }
}
