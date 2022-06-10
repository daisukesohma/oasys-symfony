<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\Program\StartProgram;
use App\Domain\Enum\EventStatusEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Domain\Util\Time;

final class StartEvent
{
    use Time;

    private EventRepository $eventRepository;
    private StartProgram $startProgram;

    public function __construct(EventRepository $eventRepository, StartProgram $startProgram)
    {
        $this->eventRepository = $eventRepository;
        $this->startProgram = $startProgram;
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function start(string $eventId): Event
    {
        $event = $this->eventRepository->mustFindOneById($eventId);
        $now = $this->getCurrentTime();

        // The 2 minute window is for accommodating the scheduled worker which might attempt to start the event
        // a minute or so before
        if ($now->modify('+2 minutes') < $this->convertTime($event->getDateEvent())) {
            throw new InvalidDateValue('Event is yet to start');
        }

        $event->setStatus(EventStatusEnum::ONGOING);
        $this->eventRepository->saveNoLog($event);

        $program = $event->getProgram();
        if ($program !== null) {
            $this->startProgram->start($program->getId());
        }

        return $event;
    }
}
