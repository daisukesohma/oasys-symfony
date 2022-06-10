<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Ville;

use App\Application\Ville\GetAllVillesFrance;
use App\Domain\Model\VilleFrance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllVillesFranceController extends AbstractController
{
    private GetAllVillesFrance $getAllVillesFrance;

    public function __construct(GetAllVillesFrance $getAllVillesFrance)
    {
        $this->getAllVillesFrance = $getAllVillesFrance;
    }

    /**
     * @return ResultIterator|VilleFrance[]
     *
     * @Query
     */
    public function getAllVillesFrance(string $search): ResultIterator
    {
        /** @var ResultIterator|VilleFrance[] $result */
        $result = $this->getAllVillesFrance->getAll($search);

        return $result;
    }
}
