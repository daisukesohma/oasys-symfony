<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\GetUsersToAssociateToProgram;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetUsersToAssociateToProgramController extends AbstractController
{
    private GetUsersToAssociateToProgram $getUsersToAssociateToProgram;

    public function __construct(GetUsersToAssociateToProgram $getUsersToAssociateToProgram)
    {
        $this->getUsersToAssociateToProgram = $getUsersToAssociateToProgram;
    }

    /**
     * @return ResultIterator|User[]
     *
     * @Query
     * @Logged
     */
    public function getUsersToAssociateToProgram(?string $search = null, ?string $companyId = null): ResultIterator
    {
        /** @var ResultIterator|User[] $result */
        $result = $this->getUsersToAssociateToProgram->getUsers($search, $companyId);

        return $result;
    }
}
