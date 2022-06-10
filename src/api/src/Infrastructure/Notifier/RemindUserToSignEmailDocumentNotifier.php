<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Document\RemindUserToSignDocumentNotifier;
use App\Domain\Model\Document;
use App\Infrastructure\Config\EnvVarHelper;

class RemindUserToSignEmailDocumentNotifier implements RemindUserToSignDocumentNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(string $email, Document $document): void
    {
        $this->mailer->send($email, 'Rappel : vous êtes invité(e) à signer un document', 'emails/document/toSign.reminder.html.twig', [
            'documentName' => $document->getName(),
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/connexion',
        ]);
    }
}
