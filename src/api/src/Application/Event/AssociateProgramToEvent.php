<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\Program\UpdateProgramEventDates;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\ProgramRepository;

final class AssociateProgramToEvent
{
    private ProgramRepository $programRepository;
    private EventRepository $eventRepository;
    private UpdateProgramEventDates $updateProgramEventDates;

    public function __construct(ProgramRepository $programRepository, EventRepository $eventRepository, UpdateProgramEventDates $updateProgramEventDates)
    {
        $this->programRepository = $programRepository;
        $this->eventRepository = $eventRepository;
        $this->updateProgramEventDates = $updateProgramEventDates;
    }

    /**
     * @param string[] $eventIds
     *
     * @throws NotFound
     */
    public function associate(string $programId, array $eventIds): Program
    {
        $program = $this->programRepository->mustFindOneById($programId);
        foreach ($eventIds as $eventId) {
            $event = $this->eventRepository->mustFindOneById($eventId);
            $event->setProgram($program);
            $this->eventRepository->save($event);
        }

        $this->updateProgramEventDates->update($programId);

        return $program;
    }
}
