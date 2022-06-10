<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Util\Time;

final class RemindFinishProgram
{
    use Time;

    private ProgramRepository $programRepository;
    private ReminderFinishProgramNotifier $reminderFinishProgramNotifier;

    public function __construct(ProgramRepository $programRepository, ReminderFinishProgramNotifier $reminderFinishProgramNotifier)
    {
        $this->programRepository = $programRepository;
        $this->reminderFinishProgramNotifier = $reminderFinishProgramNotifier;
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function notify(string $programId): Program
    {
        $program = $this->programRepository->mustFindOneById($programId);
        $now = $this->getCurrentTime();
        $programDateEnd = $program->getDateEnd();

        if (! $program->getEndSupportEmail()) {
            return $program;
        }

        if (! $programDateEnd || $now < $this->convertTime($programDateEnd)->modify('-8 days')) {
            throw new InvalidDateValue('Event is yet to start');
        }

        foreach ($program->getUsersByProgramsUsers() as $user) {
            $this->reminderFinishProgramNotifier->notify($program, $user);
        }

        return $program;
    }
}
