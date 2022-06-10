<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\UpdateUser;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class UpdateUserController extends AbstractController
{
    private UpdateUser $updateUser;
    private EnvVarHelper $envVarHelper;

    public function __construct(UpdateUser $updateUser, EnvVarHelper $envVarHelper)
    {
        $this->updateUser = $updateUser;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @param string[] $roleIds
     *
     * @throws InvalidStringValue
     * @throws NotFound
     * @throws Exist
     *
     * @Mutation()
     * @Logged()
     * @Right("ROLE_UPDATE_USER")
     */
    public function updateUser(
        string $id,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $typeId,
        array $roleIds,
        string $civility = CivilityEnum::MISTER_CODE,
        ?string $address = null,
        ?string $linkedin = null,
        ?string $function = null,
        ?string $seniorityDate = null,
        ?string $previousFunction = null,
        ?string $companyId = null,
        ?string $coachId = null,
        ?string $profilePictureId = null,
        bool $status = true,
        ?string $nFirstName = null,
        ?string $nLastName = null,
        ?string $nEmail = null,
        ?string $nPhone = null,
        ?string $service = null,
        ?string $ville = null,
        ?string $department = null,
        ?string $postCode = null,
        ?string $birthDate = null,
        ?string $cvFileId = null,
        ?string $professionalCategory = null,
        ?string $annualCompensation = null,
        ?string $coachSpeciality = null,
        ?string $userCodePostal = null,
        ?string $userDepartment = null,
        ?string $userCity = null,
        ?string $workMode = null
    ): User {
        return $this->updateUser->updateUser(
            $id,
            $firstName,
            $lastName,
            $email,
            $phone,
            $typeId,
            $roleIds,
            $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH),
            $civility,
            $address,
            $linkedin,
            $function,
            $seniorityDate,
            $previousFunction,
            $companyId,
            $coachId,
            $profilePictureId,
            $status,
            $nFirstName,
            $nLastName,
            $nEmail,
            $nPhone,
            $service,
            $ville,
            $department,
            $postCode,
            $birthDate,
            $cvFileId,
            $professionalCategory,
            $annualCompensation,
            $coachSpeciality,
            $userCodePostal,
            $userDepartment,
            $userCity,
            $workMode
        );
    }
}
