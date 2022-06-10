<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Domain\Util\Time;

final class NotifyUpcomingEvent
{
    use Time;

    private EventRepository $eventRepository;
    private EventUpcomingNotifier $eventUpcomingNotifier;

    public function __construct(EventRepository $eventRepository, EventUpcomingNotifier $eventUpcomingNotifier)
    {
        $this->eventRepository = $eventRepository;
        $this->eventUpcomingNotifier = $eventUpcomingNotifier;
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function notify(string $eventId): Event
    {
        $event = $this->eventRepository->mustFindOneById($eventId);
        $now = $this->getCurrentTime();

        if ($event->getDateEvent() === null || $now < $this->convertTime($event->getDateEvent())->modify('-1 day')) {
            throw new InvalidDateValue('Event is yet to start');
        }

        foreach ($event->getUsers() as $user) {
            $this->eventUpcomingNotifier->notify($event, $user);
        }

        return $event;
    }
}
