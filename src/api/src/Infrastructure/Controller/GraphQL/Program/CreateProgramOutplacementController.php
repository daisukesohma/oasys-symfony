<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\CreateProgramOutplacement;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramOutplacement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateProgramOutplacementController extends AbstractController
{
    private CreateProgramOutplacement $createProgramOutplacement;

    public function __construct(CreateProgramOutplacement $createProgramOutplacement)
    {
        $this->createProgramOutplacement = $createProgramOutplacement;
    }

    /**
     * @param string[] $userIds
     * @param string[] $coachIds
     *
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_PROGRAM")
     */
    public function createProgramOutplacement(string $name, string $description, array $userIds, ?array $coachIds = null, ?string $modelId = null, ?int $period = null, ?string $companyId = null, bool $endSupportEmail = true): ProgramOutplacement
    {
        return $this->createProgramOutplacement->create($name, $description, $userIds, $coachIds, $modelId, $period, $companyId, $endSupportEmail);
    }
}
