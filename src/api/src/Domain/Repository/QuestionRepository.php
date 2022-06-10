<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Question;
use TheCodingMachine\TDBM\ResultIterator;

interface QuestionRepository
{
    public function save(Question $question): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): Question;

    /**
     * @return Question[]|ResultIterator
     */
    public function findByFilters(?string $search, ?string $theme, ?string $sortColumn = null, ?string $sortDirection = 'asc'): ResultIterator;
}
