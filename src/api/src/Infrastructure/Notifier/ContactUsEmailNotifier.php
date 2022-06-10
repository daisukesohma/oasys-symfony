<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\User\ContactUsNotifier;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class ContactUsEmailNotifier implements ContactUsNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(User $user, string $comment): void
    {
        $this->mailer->send('support-digital@oasys.fr', 'Demande de contact', 'emails/user/contact.us.html.twig', [
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'comment' => $comment,
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL),
        ], [], [$user->getEmail()]);
    }
}
