<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Enum\EventStatusEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;

final class ArchiveEvent
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function archive(string $eventId): Event
    {
        $event = $this->eventRepository->mustFindOneById($eventId);

        if ($event->getStatus() !== EventStatusEnum::FINISHED) {
            throw new InvalidDateValue('Event is not finished');
        }

        $event->setStatus(EventStatusEnum::ARCHIVED);
        $this->eventRepository->saveNoLog($event);

        return $event;
    }
}
