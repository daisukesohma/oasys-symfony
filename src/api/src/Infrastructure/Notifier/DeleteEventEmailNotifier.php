<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Event\DeleteEventNotifier;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Model\Event;
use App\Domain\Model\ProgramCoachingIndividual;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class DeleteEventEmailNotifier implements DeleteEventNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(Event $event, User $user): void
    {
        $this->sendEmail($event, (string) $user->getEmail());
        $program = $event->getProgram();
        if ($event->getType() !== EventTypeEnum::TRIPARTITE || $program === null || ! ($program instanceof ProgramCoachingIndividual)) {
            return;
        }

        $this->sendEmail($event, (string) $program->getEmail());
    }

    private function sendEmail(Event $event, string $email): void
    {
        $organizer = $event->getOrganizer();
        $dateEvent = $this->mailer->convertTimeToLocal($event->getDateEvent());
        $dateEventEnd = $this->mailer->convertTimeToLocal($event->getDateEventEnd());

        $this->mailer->send($email, 'Reprenez rendez-vous avec votre coach-consultant ', 'emails/event/delete.event.html.twig', [
            'firstName' => $organizer !== null ? $organizer->getFirstName() : '',
            'lastName' => $organizer !== null ? $organizer->getLastName() : '',
            'eventDate' => $dateEvent->format('d/m/Y'),
            'eventTime' => $dateEvent->format('G:i'),
            'eventEndTime' => $dateEventEnd->format('G:i'),
            'coachEmail' => $organizer !== null ? $organizer->getEmail() : "'",
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/candidate-dashboard',
        ]);
    }
}
