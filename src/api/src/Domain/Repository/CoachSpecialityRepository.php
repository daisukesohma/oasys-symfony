<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\CoachSpeciality;
use TheCodingMachine\TDBM\ResultIterator;

interface CoachSpecialityRepository
{
    /**
     * @return ResultIterator|CoachSpeciality[]
     */
    public function findAll(): ResultIterator;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): CoachSpeciality;
}
