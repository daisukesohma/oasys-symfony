<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Document\InviteCandidateToSignDocumentNotifier;
use App\Domain\Model\Document;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class InviteCandidateToSignDocumentEmailNotifier implements InviteCandidateToSignDocumentNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(Document $document, User $user, string $memberId): void
    {
        $this->mailer->send($user->getEmail(), 'Vous êtes invité(e) à signer un document !', 'emails/document/invite.to.sign.html.twig', [
            'document' => $document,
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/document-sign/' . $memberId,
        ]);
    }
}
