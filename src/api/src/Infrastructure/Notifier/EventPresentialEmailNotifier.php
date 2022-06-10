<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Event\CreateCalendarFile;
use App\Application\Event\EventPresentialNotifier;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Model\ProgramCoachingIndividual;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class EventPresentialEmailNotifier implements EventPresentialNotifier
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
    public function notify(Event $event, User $user): void
    {
        $this->sendEmail($event, (string) $user->getEmail());
        $program = $event->getProgram();
        if ($event->getType() !== EventTypeEnum::TRIPARTITE || $program === null || ! ($program instanceof ProgramCoachingIndividual)) {
            return;
        }

        $this->sendEmail($event, (string) $program->getEmail());
    }

    /**
     * @throws NotFound
     */
    private function sendEmail(Event $event, string $email): void
    {
        $organizer = $event->getOrganizer();
        $dateEvent = $this->mailer->convertTimeToLocal($event->getDateEvent());
        $dateEventEnd = $this->mailer->convertTimeToLocal($event->getDateEventEnd());

        $attachments = [$this->mailer->createAttachment(
            'event.ics',
            $this->createCalendarFile->create($event->getId(), $this->envVarHelper->fetch(EnvVarHelper::HOST_URL))
        ),
        ];

        $this->mailer->send($email, 'Confirmation de votre rendez-vous en présentiel', 'emails/event/event.with.face.meeting.html.twig', [
            'firstName' => $organizer !== null ? $organizer->getFirstName() : '',
            'lastName' => $organizer !== null ? $organizer->getLastName() : '',
            'eventDate' => $dateEvent->format('d/m/Y'),
            'eventTime' => $dateEvent->format('G:i'),
            'eventEndTime' => $dateEventEnd->format('G:i'),
            'coachEmail' => $organizer !== null ? $organizer->getEmail() : '',
            'address' => $organizer !== null ? $organizer->getAddress() : '',
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/candidate-dashboard?event=' . $event->getId(),
        ], $attachments);
    }
}
