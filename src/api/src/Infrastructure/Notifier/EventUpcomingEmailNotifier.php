<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Event\EventUpcomingNotifier;
use App\Domain\Model\Event;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class EventUpcomingEmailNotifier implements EventUpcomingNotifier
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
        $organizer = $event->getOrganizer();
        $dateEvent = $this->mailer->convertTimeToLocal($event->getDateEvent());
        $dateEventEnd = $this->mailer->convertTimeToLocal($event->getDateEventEnd());
        $program = $event->getProgram();
        $programDateEnd = $program ? $program->getDateEnd() : null;

        $this->mailer->send($user->getEmail(), 'Demain, retrouvez votre coach-consultant à distance !', 'emails/event/event.upcoming.html.twig', [
            'firstName' => $organizer !== null ? $organizer->getFirstName() : '',
            'lastName' => $organizer !== null ? $organizer->getLastName() : '',
            'eventDate' => $dateEvent->format('d/m/Y'),
            'eventTime' => $dateEvent->format('G:i'),
            'eventEndTime' => $dateEventEnd->format('G:i'),
            'coachEmail' => $organizer !== null ? $organizer->getEmail() : '',
            'programDate' => $programDateEnd !== null ? $programDateEnd->format('D/M/Y') : '',
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/candidate-dashboard?event=' . $event->getId(),
        ]);
    }
}
