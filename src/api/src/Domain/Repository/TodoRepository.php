<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Model\Todo;
use App\Domain\Model\User;

interface TodoRepository
{
    public function save(Todo $todo): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): Todo;

    /**
     * @return Todo[]
     */
    public function findByProgram(Program $program, ?User $user): array;
}
