<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\VilleFrance;
use TheCodingMachine\TDBM\ResultIterator;

interface VillesFranceRepository
{
    /**
     * @return ResultIterator|VilleFrance[]
     */
    public function findByFilters(string $search): ResultIterator;

    public function save(VilleFrance $villeFrance): void;

    public function delete(VilleFrance $villeFrance): void;

    /**
     * @return ResultIterator|VilleFrance[]
     */
    public function findAll(): ResultIterator;
}
