<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\TransferCandidate;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class TransferCandidateController extends AbstractController
{
    private TransferCandidate $transferCandidate;

    public function __construct(TransferCandidate $transferCandidate)
    {
        $this->transferCandidate = $transferCandidate;
    }

    /**
     * @throws NotFound
     *
     * @Mutation()
     * @Logged()
     */
    public function transferCandidate(string $userId, string $coachSpeciality): User
    {
        return $this->transferCandidate->transfer($userId, $coachSpeciality);
    }
}
