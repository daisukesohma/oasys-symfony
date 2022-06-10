<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Program\FinishSignatureNotifier;
use App\Domain\Model\Document;
use App\Infrastructure\Config\EnvVarHelper;

class FinishSignatureEmailNotifier implements FinishSignatureNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(string $mail, Document $document): void
    {
        $this->mailer->send($mail, 'La procédure de signature est terminée !', 'emails/document/toSign.finished.html.twig', [
            'documentName' => $document->getName(),
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/connexion',
        ]);
    }
}
