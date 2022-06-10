<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\ProgramRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetProgramsForAutocomplete
{
    private ProgramRepository $programRepository;
    private CompanyRepository $companyRepository;

    public function __construct(ProgramRepository $programRepository, CompanyRepository $companyRepository)
    {
        $this->programRepository = $programRepository;
        $this->companyRepository = $companyRepository;
    }

    public function get(?string $search): ResultIterator
    {
        return $this->programRepository->getProgramsForAutocomplete($search);
    }
}
