<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\CreateProgramPic;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateProgramPicController extends AbstractController
{
    private CreateProgramPic $createProgram;
    private TokenStorageInterface $tokenStorage;

    public function __construct(CreateProgramPic $createProgram, TokenStorageInterface $tokenStorage)
    {
        $this->createProgram = $createProgram;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string[] $coachIds
     *
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_PROGRAM")
     */
    public function createProgramPic(string $name, string $description, array $coachIds = [], ?string $modelId = null, ?int $period = null, ?string $companyId = null, bool $endSupportEmail = true, int $appointmentTimeLimit = 0, ?string $inscriptionText = null): Program
    {
        return $this->createProgram->create($name, $description, $coachIds, $modelId, $period, $companyId, $endSupportEmail, $appointmentTimeLimit, $inscriptionText);
    }
}
