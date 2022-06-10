<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\UpdateProgramPic;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\InvalidUsersXlsx;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Infrastructure\Config\EnvVarHelper;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class UpdateProgramPicController extends AbstractController
{
    private UpdateProgramPic $updateProgram;
    private TokenStorageInterface $tokenStorage;
    private EnvVarHelper $envVarHelper;

    public function __construct(UpdateProgramPic $updateProgram, TokenStorageInterface $tokenStorage, EnvVarHelper $envVarHelper)
    {
        $this->updateProgram = $updateProgram;
        $this->tokenStorage = $tokenStorage;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @param string[] $coachIds
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidStringValue
     * @throws InvalidUsersXlsx
     * @throws InvalidFileValue
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_PROGRAM")
     */
    public function updateProgramPic(string $id, string $name, string $description, array $coachIds, array $userIds = [], ?string $modelId = null, ?int $period = null, ?UploadedFileInterface $file = null, ?string $companyId = null, bool $endSupportEmail = true, int $appointmentTimeLimit = 0, ?string $inscriptionText = null): Program
    {
        return $this->updateProgram->update($id, $name, $description, $coachIds, $userIds, $modelId, $period, $file, $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH), $companyId, $endSupportEmail, $appointmentTimeLimit, $inscriptionText);
    }
}
