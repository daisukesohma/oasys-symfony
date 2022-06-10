<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\Queue\QueueMessagingManager;
use App\Domain\Enum\EventStatusEnum;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function time;

final class UnsubscribeCandidateFromBookedAppointment
{
    private EventRepository $eventRepository;
    private UserRepository $userRepository;
    private QueueMessagingManager $queueMessagingManager;
    private DeleteEventNotifier $deleteEventNotifier;
    private DeleteEventCoachNotifier $deleteEventCoachNotifier;

    public function __construct(EventRepository $eventRepository, UserRepository $userRepository, QueueMessagingManager $queueMessagingManager, DeleteEventNotifier $deleteEventNotifier, DeleteEventCoachNotifier $deleteEventCoachNotifier)
    {
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
        $this->queueMessagingManager = $queueMessagingManager;
        $this->deleteEventNotifier = $deleteEventNotifier;
        $this->deleteEventCoachNotifier = $deleteEventCoachNotifier;
    }

    /**
     * @throws NotFound
     */
    public function unsubscribeAppointment(string $eventId, string $userId): Event
    {
        $event = $this->eventRepository->mustFindOneById($eventId);
        $user = $this->userRepository->mustFindOneById($userId);

        // This use-case is only used for Individual Session or Workshop events
        if ((
                $event->getType() !== EventTypeEnum::INDIVIDUAL_SESSION
            ) || (
                $user->getId() !== $this->userRepository->getLoggedUser()->getId()
            ) || (
                $event->getDateEvent() !== null && $event->getDateEvent()->getTimestamp() <= time()
            )
        ) {
            throw new AccessDeniedException('Cannot access this event');
        }

        $event->removeUser($user);
        $this->eventRepository->save($event);

        $hasAppointmentEvents = false;
        foreach ($user->getEventsByEventsUsers() as $userEvent) {
            if ($userEvent->getId() === $event->getId() || $userEvent->getType() !== EventTypeEnum::INDIVIDUAL_SESSION) {
                continue;
            }

            $hasAppointmentEvents = true;
        }

        if ($event->getType() === EventTypeEnum::INDIVIDUAL_SESSION
            && $user->getType()->getId() === UserTypeEnum::CANDIDATE
            && $event->getStatus() === EventStatusEnum::UPCOMING) {
            if (! $hasAppointmentEvents) {
                $user->setAppointmentBooked(false);
                $user->setCoach(null);
                $this->userRepository->save($user);
            }

            $event->setStatus(EventStatusEnum::CREATED);
            $event->setCoachSpeciality(null);
        }

        $this->eventRepository->save($event);

        $this->deleteEventNotifier->notify($event, $user);
        $this->deleteEventCoachNotifier->notify($event, $user);

        return $event;
    }
}
