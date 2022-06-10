<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\User\CreateUserNotifier;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class CreateUserEmailNotifier implements CreateUserNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(User $user, string $tokenPassword): void
    {
        $this->mailer->send($user->getEmail(), 'Bienvenue !', 'emails/user/create.user.html.twig', [
            'user' => $user,
            'update_password_url'=>
                $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/update-password?token=' . $tokenPassword,
        ]);
    }
}
