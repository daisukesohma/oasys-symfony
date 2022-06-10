<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\Program\FinishProgram;
use App\Domain\Enum\EventStatusEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Domain\Util\Time;

final class FinishEvent
{
    use Time;

    private EventRepository $eventRepository;
    private EventFinishedNotifier $eventFinishedNotifier;
    private FinishProgram $finishProgram;

    public function __construct(EventRepository $eventRepository, EventFinishedNotifier $eventFinishedNotifier, FinishProgram $finishProgram)
    {
        $this->eventRepository = $eventRepository;
        $this->finishProgram = $finishProgram;
        $this->eventFinishedNotifier = $eventFinishedNotifier;
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function finish(string $eventId): Event
    {
        $event = $this->eventRepository->mustFindOneById($eventId);
        $now = $this->getCurrentTime();

        if ($now < $this->convertTime($event->getDateEvent())) {
            throw new InvalidDateValue('Event has not started');
        }

        $event->setStatus(EventStatusEnum::FINISHED);
        $this->eventRepository->saveNoLog($event);

        if ($event->getProgram() !== null) {
            $program = $event->getProgram();

            $allEventsFinished = true;
            foreach ($program->getEvents() as $event) {
                if ($event->getStatus() === EventStatusEnum::FINISHED || $event->getStatus() === EventStatusEnum::ARCHIVED) {
                    continue;
                }

                $allEventsFinished = false;
            }

            if ($allEventsFinished) {
                $this->finishProgram->finish($program->getId());
            }
        }

        foreach ($event->getUsers() as $user) {
            $this->eventFinishedNotifier->notify($event, $user);
        }

        return $event;
    }
}
