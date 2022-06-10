<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Repository\ProgramCoachingIndividualRepository;
use App\Domain\Repository\ProgramRepository;

final class DeleteProgram
{
    private ProgramRepository $programRepository;
    private ProgramCoachingIndividualRepository $programCoachingIndividualRepository;

    public function __construct(ProgramRepository $programRepository, ProgramCoachingIndividualRepository $programCoachingIndividualRepository)
    {
        $this->programRepository = $programRepository;
        $this->programCoachingIndividualRepository = $programCoachingIndividualRepository;
    }

    /**
     * @throws NotFound
     */
    public function delete(string $id): Program
    {
        $program = $this->programRepository->mustFindOneById($id);
        $program->setDeleted(true);
        $this->programRepository->save($program);

        return $program;
    }
}
