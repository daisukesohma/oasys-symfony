<?php

declare(strict_types=1);

namespace App\Application\Ville;

use App\Domain\Model\VilleFrance;
use App\Domain\Repository\VillesFranceRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllVillesFrance
{
    private VillesFranceRepository $villesFranceRepository;

    public function __construct(VillesFranceRepository $villesFranceRepository)
    {
        $this->villesFranceRepository = $villesFranceRepository;
    }

    /**
     * @return ResultIterator|VilleFrance[]
     */
    public function getAll(string $search): ResultIterator
    {
        return $this->villesFranceRepository->findByFilters($search);
    }
}
