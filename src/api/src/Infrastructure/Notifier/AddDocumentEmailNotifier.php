<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Document\AddDocumentNotifier;
use App\Domain\Model\Document;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class AddDocumentEmailNotifier implements AddDocumentNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(Document $document, User $user): void
    {
        $this->mailer->send($user->getEmail(), 'Nouveau document disponible !', 'emails/document/create.html.twig', [
            'documentName' => $document->getName(),
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/connexion',
        ]);
    }
}
