<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\UpdateProgram;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class UpdateProgramController extends AbstractController
{
    private UpdateProgram $updateProgram;
    private TokenStorageInterface $tokenStorage;

    public function __construct(UpdateProgram $updateProgram, TokenStorageInterface $tokenStorage)
    {
        $this->updateProgram = $updateProgram;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string[] $userIds
     * @param string[] $coachIds
     *
     * @throws NotFound
     * @throws InvalidStringValue
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_UPDATE_PROGRAM")
     */
    public function updateProgram(string $id, string $name, string $description, string $type, array $userIds, ?array $coachIds = null, ?string $modelId = null, ?string $firstName = null, ?string $lastName = null, ?string $email = null, ?string $phone = null, ?string $dateStart = null, ?string $dateEnd = null, ?int $period = null, ?string $companyId = null, bool $endSupportEmail = true): Program
    {
        return $this->updateProgram->update($id, $name, $description, $type, $userIds, $coachIds, $modelId, $firstName, $lastName, $email, $phone, $dateStart, $dateEnd, $period, $companyId, $endSupportEmail);
    }
}
