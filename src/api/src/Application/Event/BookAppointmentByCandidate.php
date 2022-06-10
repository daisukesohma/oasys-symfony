<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\Queue\QueueMessagingManager;
use App\Domain\Enum\EventMeetingEnum;
use App\Domain\Enum\EventStatusEnum;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function time;

final class BookAppointmentByCandidate
{
    private EventRepository $eventRepository;
    private UserRepository $userRepository;
    private QueueMessagingManager $queueMessagingManager;
    private EventPresentialNotifier $eventPresentialNotifier;
    private EventVisioNotifier $eventVisioNotifier;
    private EventAppointmentCoachNotifier $eventAppointmentCoachEmailNotifier;

    public function __construct(EventRepository $eventRepository, UserRepository $userRepository, QueueMessagingManager $queueMessagingManager, EventPresentialNotifier $eventPresentialNotifier, EventVisioNotifier $eventVisioNotifier, EventAppointmentCoachNotifier $eventAppointmentCoachEmailNotifier)
    {
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
        $this->queueMessagingManager = $queueMessagingManager;
        $this->eventPresentialNotifier = $eventPresentialNotifier;
        $this->eventVisioNotifier = $eventVisioNotifier;
        $this->eventAppointmentCoachEmailNotifier = $eventAppointmentCoachEmailNotifier;
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function bookAppointment(string $eventId, string $userId): Event
    {
        $event = $this->eventRepository->mustFindOneById($eventId);
        $user = $this->userRepository->mustFindOneById($userId);

        // This use-case is only used for Individual Session or Workshop events
        if ((
                $event->getType() !== EventTypeEnum::WORKSHOP
                && $event->getType() !== EventTypeEnum::INDIVIDUAL_SESSION
            ) || (
                $this->userRepository->getLoggedUser()->getType()->getId() === UserTypeEnum::CANDIDATE
                && $user->getId() !== $this->userRepository->getLoggedUser()->getId()
            ) || (
                $event->getDateEvent() !== null && $event->getDateEvent()->getTimestamp() <= time()
            ) || ($event->getType() === EventTypeEnum::INDIVIDUAL_SESSION && $event->getStatus() !== EventStatusEnum::CREATED)
        ) {
            throw new AccessDeniedException('Cannot access this event');
        }

        $event->addUser($user);

        if ($event->getType() === EventTypeEnum::INDIVIDUAL_SESSION
            && $user->getType()->getId() === UserTypeEnum::CANDIDATE
            && $event->getStatus() === EventStatusEnum::CREATED) {
            $user->setAppointmentBooked(true);
            $user->setHasBeenTransferred(false);
            $user->setCoach($event->getOrganizer());

            $this->userRepository->save($user);

            $event->setStatus(EventStatusEnum::UPCOMING);
        }

        $organizer = $event->getOrganizer();
        if ($organizer !== null) {
            $event->setCoachSpeciality($organizer->getCoachSpeciality());
        }

        $this->eventRepository->save($event);

        $this->queueMessagingManager->queue($event->getId());

        if ($event->getMeetingPlace() === EventMeetingEnum::PRESENTIAL) {
            $this->eventPresentialNotifier->notify($event, $user);
        } else {
            $this->eventVisioNotifier->notify($event, $user);
        }

        $this->eventAppointmentCoachEmailNotifier->notify($event, $user);

        return $event;
    }
}
