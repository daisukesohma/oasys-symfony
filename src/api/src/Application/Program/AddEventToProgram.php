<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Exception\NotFound;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\ProgramRepository;

final class AddEventToProgram
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
     * @throws NotFound
     */
    public function add(string $programId, string $eventId): void
    {
        $program = $this->programRepository->mustFindOneById($programId);
        $event = $this->eventRepository->mustFindOneById($eventId);

        $event->setProgram($program);
        $this->eventRepository->save($event);

        $this->updateProgramEventDates->update($programId);
    }
}
