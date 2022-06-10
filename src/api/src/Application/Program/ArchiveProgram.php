<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Enum\ProgramStatusEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Repository\ProgramRepository;

final class ArchiveProgram
{
    private ProgramRepository $programRepository;

    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function archive(string $programId): Program
    {
        $program = $this->programRepository->mustFindOneById($programId);

        if ($program->getStatus() !== ProgramStatusEnum::FINISHED) {
            throw new InvalidDateValue('Program is not finished');
        }

        $program->setStatus(ProgramStatusEnum::ARCHIVED);
        $this->programRepository->saveNoLog($program);

        return $program;
    }
}
