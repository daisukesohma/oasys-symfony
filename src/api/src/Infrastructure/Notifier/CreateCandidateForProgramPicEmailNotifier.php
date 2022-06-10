<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\User\CreateCandidateForProgramPicNotifier;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class CreateCandidateForProgramPicEmailNotifier implements CreateCandidateForProgramPicNotifier
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
        $this->mailer->send($user->getEmail(), 'Confirmation d’inscription', 'emails/user/create.candidate.program.pic.html.twig', [
            'user' => $user,
            'update_password_url'=>
                $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/update-password?token=' . $tokenPassword,
        ]);
    }
}
