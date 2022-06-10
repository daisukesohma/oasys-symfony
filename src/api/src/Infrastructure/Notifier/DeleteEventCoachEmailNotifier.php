<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Event\DeleteEventCoachNotifier;
use App\Domain\Model\Event;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class DeleteEventCoachEmailNotifier implements DeleteEventCoachNotifier
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
        if ($organizer === null) {
            return;
        }
        $dateEvent = $this->mailer->convertTimeToLocal($event->getDateEvent());
        $dateEventEnd = $this->mailer->convertTimeToLocal($event->getDateEventEnd());
        $company = $user->getCompany();

        $this->mailer->send($organizer->getEmail(), 'Annulation du rendez-vous ', 'emails/event/delete.event.coach.html.twig', [
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'companyName' => $company !== null ? $company->getName() : '',
            'eventDate' => $dateEvent->format('d/m/Y'),
            'eventTime' => $dateEvent->format('G:i'),
            'eventEndTime' => $dateEventEnd->format('G:i'),
            'coachEmail' => $organizer->getEmail(),
            'link' => $this->envVarHelper->fetch(EnvVarHelper::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(EnvVarHelper::HOST_URL) . '/candidate-dashboard',
        ]);
    }
}
