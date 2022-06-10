<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\CreateProgramCoachingIndividual;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramCoachingIndividual;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateProgramCoachingIndividualController extends AbstractController
{
    private CreateProgramCoachingIndividual $createProgram;
    private TokenStorageInterface $tokenStorage;

    public function __construct(CreateProgramCoachingIndividual $createProgram, TokenStorageInterface $tokenStorage)
    {
        $this->createProgram = $createProgram;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_PROGRAM")
     */
    public function createProgramCoachingIndividual(
        string $name,
        string $description,
        string $type,
        array $userIds,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        ?string $coachId = null,
        ?string $modelId = null,
        ?int $period = null,
        ?string $companyId = null,
        bool $endSupportEmail = true
    ): ProgramCoachingIndividual {
        return $this->createProgram->create($name, $description, $type, $userIds, $firstName, $lastName, $email, $phone, $coachId, $modelId, $period, $companyId, $endSupportEmail);
    }
}
