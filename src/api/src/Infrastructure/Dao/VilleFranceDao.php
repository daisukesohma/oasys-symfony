<?php
/*
 * This file has been automatically generated by TDBM.
 * You can edit this file as it will not be overwritten.
 */

declare(strict_types=1);

namespace App\Infrastructure\Dao;

use App\Domain\Model\VilleFrance;
use App\Domain\Repository\VillesFranceRepository;
use App\Infrastructure\Dao\Generated\AbstractVilleFranceDao;
use TheCodingMachine\TDBM\ResultIterator;

/**
 * The VilleFranceDao class will maintain the persistence of VilleFrance class into the villes_france table.
 */
class VilleFranceDao extends AbstractVilleFranceDao implements VillesFranceRepository
{
    /**
     * @return ResultIterator|VilleFrance[]
     */
    public function findByFilters(string $search): ResultIterator
    {
        return $this->find('code_postal LIKE :search', ['search' => $search . '%']);
    }
}
