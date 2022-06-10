<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Event\CreateCalendarFile;
use App\Application\Event\EventAppointmentCoachNotifier;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;
use function strtoupper;

class EventAppointmentCoachEmailNotifier implements EventAppointmentCoachNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;
    private CreateCalendarFile $createCalendarFile;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper, CreateCalendarFile $createCalendarFile)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
        $this->createCalendarFile = $createCalendarFile;
    }

    /**
     * @throws NotFound
     */
    public function notify(Event $event, User $candidate): void
    {
        $organizer = $event->getOrganizer();
        if ($organizer === null) {
            return;
        }

        $dateEvent = $this->mailer->convertTimeToLocal($event->getDateEvent());
        $dateEventEnd = $this->mailer->convertTimeToLocal($event->getDateEventEnd());

        $attachments = [$this->mailer->createAttachment(
            'event.ics',
            $this->createCalendarFile->create($event->getId(), $this->envVarHelper->fetch(EnvVarHelper::HOST_URL), $candidate)
        ),
        ];

        $this->mailer->send($organizer->getEmail(), 'Planification du rendez-vous avec "' . $candidate->getFirstName() . ' ' . strtoupper($candidate->getLastName()) . '"', 'emails/event/event.appointment.coach.html.twig', [
            'firstName' => $candidate->getFirstName(),
            'lastName' => $candidate->getLastName(),
            'eventDate' => $dateEvent->format('d/m/Y'),
            'eventTime' => $dateEvent->format('G:i'),
            'eventEndTime' => $dateEventEnd->format('G:i'),
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/connexion',
        ], $attachments);
    }
}
