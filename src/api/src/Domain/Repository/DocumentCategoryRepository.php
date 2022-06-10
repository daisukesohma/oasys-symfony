<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\DocumentCategory;
use App\Domain\Model\User;
use TheCodingMachine\TDBM\ResultIterator;

interface DocumentCategoryRepository
{
    /**
     * @return ResultIterator|DocumentCategory[]
     */
    public function findAll(): ResultIterator;

    /**
     * @return ResultIterator|DocumentCategory[]
     */
    public function getAll(User $currentUser): ResultIterator;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): DocumentCategory;
}
