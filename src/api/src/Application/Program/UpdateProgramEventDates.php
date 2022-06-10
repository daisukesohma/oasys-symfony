<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Exception\NotFound;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\ProgramRepository;

final class UpdateProgramEventDates
{
    private ProgramRepository $programRepository;
    private EventRepository $eventRepository;

    public function __construct(ProgramRepository $programRepository, EventRepository $eventRepository)
    {
        $this->programRepository = $programRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * @throws NotFound
     */
    public function update(string $programId): void
    {
        $program = $this->programRepository->mustFindOneById($programId);

        $events = $program->getEvents();
        $minDate = null;
        $maxDate = null;
        foreach ($events as $event) {
            if (! $event->getDateEvent()) {
                continue;
            }
            if (! $maxDate || $event->getDateEventEnd() > $maxDate) {
                $maxDate = $event->getDateEventEnd();
            }
            if ($minDate && $event->getDateEvent() >= $minDate) {
                continue;
            }

            $minDate = $event->getDateEvent();
        }
        $program->setDateStart($minDate);
        if ($program->getPeriod() === null || $maxDate === null) {
            $program->setDateEnd($maxDate);
        } elseif ($minDate !== null) {
            $program->setDateEnd($minDate->modify('+' . $program->getPeriod() . ' months'));
        }

        $this->programRepository->save($program);
    }
}
