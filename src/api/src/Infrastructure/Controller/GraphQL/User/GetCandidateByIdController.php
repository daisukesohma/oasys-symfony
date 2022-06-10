<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\GetCandidateById;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Candidate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetCandidateByIdController extends AbstractController
{
    private GetCandidateById $getCandidateById;

    public function __construct(GetCandidateById $getCandidateById)
    {
        $this->getCandidateById = $getCandidateById;
    }

    /**
     * @throws NotFound
     *
     * @Query
     * @Logged
     */
    public function getCandidateById(string $id, ?string $programId = null): Candidate
    {
        return $this->getCandidateById->get($id, $programId);
    }
}
