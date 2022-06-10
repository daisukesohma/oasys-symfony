<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Route;

use App\Application\Document\ReminderDocumentToSign;
use App\Application\Document\UpdateDocumentSignerStatus;
use App\Application\Document\UpdateFileDocument;
use App\Application\Document\UpdateSignDocument;
use App\Domain\Enum\EventYouSignEnum;
use App\Domain\Enum\ProcedureYouSignStatusEnum;
use App\Domain\Exception\EmailError;
use App\Domain\Exception\NotFound;
use App\Infrastructure\Config\EnvVarHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function explode;
use function is_string;
use function Safe\json_decode;

class YouSignWebHookController extends AbstractController
{
    private UpdateSignDocument $updateSignDocument;
    private UpdateFileDocument $updateFileDocument;
    private UpdateDocumentSignerStatus $updateDocumentSignerStatus;
    private ReminderDocumentToSign $reminderDocumentToSign;
    private EnvVarHelper $envVarHelper;

    public function __construct(
        UpdateSignDocument $signContract,
        UpdateFileDocument $updateFileDocument,
        UpdateDocumentSignerStatus $updateDocumentSigner,
        ReminderDocumentToSign $reminderDocumentToSign,
        EnvVarHelper $envVarHelper
    ) {
        $this->updateSignDocument = $signContract;
        $this->updateFileDocument = $updateFileDocument;
        $this->updateDocumentSignerStatus = $updateDocumentSigner;
        $this->reminderDocumentToSign = $reminderDocumentToSign;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @throws NotFound
     * @throws EmailError
     *
     * @Route("/webhook/yousign", name="handle")
     */
    public function handle(Request $request): Response
    {
        $response = $request->getContent(false);
        if (! is_string($response)) {
            return new Response('false');
        }

        $webHookEvent = json_decode($response, true);

        if ($webHookEvent['eventName'] === EventYouSignEnum::REMINDER_EXECUTED) {
            $this->reminderDocumentToSign->reminderDocumentToSign($webHookEvent['procedure']['members'], $webHookEvent['procedure']['id']);
        }

        if ($webHookEvent['eventName'] === EventYouSignEnum::PROCEDURE_FINISHED) {
            $this->updateSignDocument->updateSignDocument($webHookEvent['procedure']['id']);
        }

        if ($webHookEvent['eventName'] === EventYouSignEnum::MEMBER_FINISHED || $webHookEvent['eventName'] === EventYouSignEnum::PROCEDURE_FINISHED) {
            foreach ($webHookEvent['procedure']['members'] as $member) {
                if (empty(explode('/', $member['id'])[2])) {
                    continue;
                }

                $memberId = explode('/', $member['id'])[2];

                if ($member['status'] !== ProcedureYouSignStatusEnum::MEMBER_DONE) {
                    continue;
                }

                $this->updateDocumentSignerStatus->updateDocumentSigner($webHookEvent['procedure']['id'], $memberId);
            }

            $this->updateFileDocument->updateFileDocument($webHookEvent['procedure']['id'], $webHookEvent['procedure']['files'][0]['id'], $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH));
        }

        return new Response('true');
    }
}
