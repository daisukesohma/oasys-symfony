<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Program\ReminderFinishProgramNotifier;
use App\Domain\Model\Program;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class ReminderFinishProgramEmailNotifier implements ReminderFinishProgramNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(Program $program, User $user): void
    {
        $this->mailer->send($user->getEmail(), 'Fin de votre accompagnement dans une semaine', 'emails/program/program.finish.html.twig', [
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/connexion',
        ]);
    }
}
