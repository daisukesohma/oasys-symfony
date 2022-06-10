<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramCoachingIndividual;

interface ProgramCoachingIndividualRepository
{
    public function save(ProgramCoachingIndividual $programCoachingIndividual): void;

    public function delete(ProgramCoachingIndividual $programCoachingIndividual): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): ProgramCoachingIndividual;

    public function findOneById(string $id): ?ProgramCoachingIndividual;
}
