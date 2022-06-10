<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Event\CreateCalendarFile;
use App\Application\Event\EventDateChangeNotifier;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Model\ProgramCoachingIndividual;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;
use DateTimeImmutable;

class EventDateChangeEmailNotifier implements EventDateChangeNotifier
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
    public function notify(Event $event, User $user, DateTimeImmutable $previousDateEvent): void
    {
        $this->sendEmail($event, (string) $user->getEmail(), $previousDateEvent);
        $program = $event->getProgram();
        if ($event->getType() !== EventTypeEnum::TRIPARTITE || $program === null || ! ($program instanceof ProgramCoachingIndividual)) {
            return;
        }

        $this->sendEmail($event, (string) $program->getEmail(), $previousDateEvent);
    }

    /**
     * @throws NotFound
     */
    private function sendEmail(Event $event, string $email, DateTimeImmutable $previousDateEvent): void
    {
        $organizer = $event->getOrganizer();
        $dateEvent = $this->mailer->convertTimeToLocal($event->getDateEvent());
        $dateEventEnd = $this->mailer->convertTimeToLocal($event->getDateEventEnd());
        $previousDateEvent = $this->mailer->convertTimeToLocal($previousDateEvent);

        $attachments = [$this->mailer->createAttachment(
            $event->getName() . '.ics',
            $this->createCalendarFile->create($event->getId(), $this->envVarHelper->fetch(EnvVarHelper::HOST_URL)),
        ),
        ];

        $this->mailer->send($email, 'Confirmation de modification de votre rendez-vous', 'emails/event/event.date.change.html.twig', [
            'firstName' => $organizer !== null ? $organizer->getFirstName() : '',
            'lastName' => $organizer !== null ? $organizer->getLastName() : '',
            'eventDate' => $dateEvent->format('d/m/Y'),
            'eventTime' => $dateEvent->format('G:i'),
            'eventPlace' => $event->getMeetingPlace(),
            'eventEndTime' => $dateEventEnd->format('G:i'),
            'coachEmail' => $organizer !== null ? $organizer->getEmail() : '',
            'previousDate' => $previousDateEvent->format('d/m/Y'),
            'previousTime' => $previousDateEvent->format('G:i'),
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/candidate-dashboard?event=' . $event->getId(),
        ], $attachments);
    }
}
