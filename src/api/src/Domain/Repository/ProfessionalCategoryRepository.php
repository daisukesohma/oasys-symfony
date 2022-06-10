<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\ProfessionalCategory;
use TheCodingMachine\TDBM\ResultIterator;

interface ProfessionalCategoryRepository
{
    /**
     * @return ResultIterator|ProfessionalCategory[]
     */
    public function findAll(): ResultIterator;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): ProfessionalCategory;

    /**
     * @throws NotFound
     */
    public function mustFindOneByIdOrLabel(string $id): ProfessionalCategory;
}
