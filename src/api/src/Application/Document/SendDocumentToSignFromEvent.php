<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\ProcedureYouSignStatusEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Document;
use App\Domain\Model\Event;
use App\Domain\Model\User;
use function count;

class SendDocumentToSignFromEvent
{
    private SendDocumentToSign $sendDocumentToSign;

    public function __construct(
        SendDocumentToSign $sendDocumentToSign
    ) {
        $this->sendDocumentToSign = $sendDocumentToSign;
    }

    /**
     * @throws YouSignError
     * @throws NotFound
     */
    public function sendDocumentToSignFromEvent(Event $event, Document $document, string $webHookYouSignUrl, string $rootPath): void
    {
        if (! $document->getToBeSigned()) {
            return;
        }

        if (! count($event->getUsers())) {
            throw new NotFound(User::class, ['User for event' => $event->getId()]);
        }
        $user = $event->getUsers()[0];
        $organizer = $event->getOrganizer();

        if ($organizer === null) {
            throw new NotFound(User::class, ['Organizer for event' => $event->getId()]);
        }

        $members = [
            [
                'user' => $organizer,
                'validator' => true,
                'firstname' => $organizer->getFirstName(),
                'lastname' => $organizer->getLastName(),
                'email' => $organizer->getEmail(),
                'phone' => $organizer->getPhone(),
                'page' => 1,
                'position' => '467,50,566,89',
                'mention' => '',
                'mention2' => '',
                'reason' => '',
            ],
            [
                'user' => $user,
                'firstname' => $user->getFirstName(),
                'lastname' => $user->getLastName(),
                'email' => $user->getEmail(),
                'phone' => $user->getPhone(),
                'page' => 1,
                'position' => '14,53,112,93',
                'mention' => '',
                'mention2' => '',
                'reason' => '',
            ],
        ];

        $result = $this->sendDocumentToSign->sendDocumentToSign($document, $members, $webHookYouSignUrl, $rootPath);

        $document->setStatusSignature(ProcedureYouSignStatusEnum::PROCEDURE_PENDING);
        $document->setToBeSigned(true);

        if (! $result) {
            throw new YouSignError('An error happen with YouSign API');
        }
    }
}
