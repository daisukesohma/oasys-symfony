<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Event\EventFinishedNotifier;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Model\Event;
use App\Domain\Model\ProgramCoachingIndividual;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class EventFinishedEmailNotifier implements EventFinishedNotifier
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
        $program = $event->getProgram();
        $programDateEnd = $program ? $program->getDateEnd() : null;
        $hostProtocol = $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL);
        $hostUrl = $this->envVarHelper->fetch(EnvVarHelper::HOST_URL);

        $this->mailer->send($email, 'Enquête d\'évaluation : votre avis nous intéresse !', 'emails/event/event.finished.html.twig', [
            'firstName' => $organizer !== null ? $organizer->getFirstName() : '',
            'lastName' => $organizer !== null ? $organizer->getLastName() : '',
            'eventDate' => $dateEvent->format('d/m/Y'),
            'eventTime' => $dateEvent->format('G:i'),
            'eventEndTime' => $dateEventEnd->format('G:i'),
            'coachEmail' => $organizer !== null ? $organizer->getEmail() : '',
            'link' => $hostProtocol . '://' . $hostUrl,
            'programDate' => $programDateEnd !== null ? $programDateEnd->format('d/m/Y') : '',
            'linkSurvey' => $hostProtocol . '://' . $hostUrl,
            'linkEventDashboard' => $hostProtocol . '://' . $hostUrl . '/candidate-dashboard?event=' . $event->getId(),
        ]);
    }
}
