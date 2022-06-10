<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\ProgramOutplacement;

interface ProgramOutplacementRepository
{
    public function save(ProgramOutplacement $programOutplacement): void;

    public function delete(ProgramOutplacement $programOutplacement): void;
}
