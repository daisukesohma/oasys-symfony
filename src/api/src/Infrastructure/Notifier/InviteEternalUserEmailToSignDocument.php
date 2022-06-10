<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Document\InviteExternalUserToSignDocument;
use App\Domain\Model\Document;
use App\Infrastructure\Config\EnvVarHelper;

class InviteEternalUserEmailToSignDocument implements InviteExternalUserToSignDocument
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(Document $document, string $email, string $firstName, string $lastname, string $memberId): void
    {
        $this->mailer->send($email, 'Vous êtes invité(e) à signer un document !', 'emails/document/toSignExternalUser.html.twig', [
            'document' => $document,
            'firstName' => $firstName,
            'lastName' => $lastname,
            'memberId' => $memberId,
            'link' => $this->envVarHelper->fetch(EnvVarHelper::YOUSIGN_APP) . '/procedure/sign?members=/members/' . $memberId,
        ]);
    }
}
