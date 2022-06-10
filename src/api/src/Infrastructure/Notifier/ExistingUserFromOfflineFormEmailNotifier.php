<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\User\ExistingUserFromOfflineFormNotifier;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class ExistingUserFromOfflineFormEmailNotifier implements ExistingUserFromOfflineFormNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(User $user, string $id): void
    {
        $this->mailer->send(
            $this->envVarHelper->fetch(EnvVarHelper::SUPPORT_EMAIL),
            'Informations pour l\'utilisateur invité avec l\'e-mail existant "' . $user->getEmail() . '"',
            'emails/user/existing.user.invited.html.twig',
            [
                'user' => $user,
                'url' =>
                    $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/user/' . $id,
            ],
        );
    }
}
