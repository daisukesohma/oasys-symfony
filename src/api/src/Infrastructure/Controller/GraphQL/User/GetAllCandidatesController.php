<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\GetAllCandidates;
use App\Domain\Model\Candidate;
use Porpaginas\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetAllCandidatesController extends AbstractController
{
    private GetAllCandidates $getAllCandidates;

    public function __construct(GetAllCandidates $getAllCandidates)
    {
        $this->getAllCandidates = $getAllCandidates;
    }

    /**
     * @param string[]|null $eventStatuses
     *
     * @return Result|Candidate[]
     *
     * @Query
     * @Logged
     */
    public function getAllCandidates(?string $email, ?string $lastName, ?string $firstName, ?array $eventStatuses = null, ?string $programType = null, ?string $eventType = null, ?string $date = null): Result
    {
        /** @var Result|Candidate[] $result */
        $result = $this->getAllCandidates->getAll($email, $lastName, $firstName, $eventStatuses, $programType, $eventType, $date);

        return $result;
    }
}
