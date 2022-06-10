<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Enum\ProgramStatusEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Util\Time;

final class StartProgram
{
    use Time;

    private ProgramRepository $programRepository;

    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function start(string $programId): Program
    {
        $program = $this->programRepository->mustFindOneById($programId);
        $now = $this->getCurrentTime();

        if ($now->modify('+2 minutes') < $this->convertTime($program->getDateStart())) {
            throw new InvalidDateValue('Program is yet to start');
        }

        $program->setStatus(ProgramStatusEnum::INPROGRESS);
        $this->programRepository->saveNoLog($program);

        return $program;
    }
}
