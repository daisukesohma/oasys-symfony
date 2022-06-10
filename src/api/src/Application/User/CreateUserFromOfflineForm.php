<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Enum\CivilityEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\ProfessionalCategoryRepository;
use App\Domain\Repository\ProgramPicRepository;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use Safe\DateTimeImmutable;

final class CreateUserFromOfflineForm
{
    private UserRepository $userRepository;
    private UserTypeRepository $userTypeRepository;
    private ExistingUserFromOfflineFormNotifier $existingUserFromOfflineFormNotifier;
    private ProgramPicRepository $programRepository;
    private ProfessionalCategoryRepository $professionalCategoryRepository;
    private RoleRepository $roleRepository;
    private CompanyRepository $companyRepository;

    public function __construct(
        UserRepository $userRepository,
        UserTypeRepository $userTypeRepository,
        ExistingUserFromOfflineFormNotifier $existingUserFromOfflineFormNotifier,
        ProgramPicRepository $programRepository,
        ProfessionalCategoryRepository $professionalCategoryRepository,
        RoleRepository $roleRepository,
        CompanyRepository $companyRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userTypeRepository = $userTypeRepository;
        $this->existingUserFromOfflineFormNotifier = $existingUserFromOfflineFormNotifier;
        $this->programRepository = $programRepository;
        $this->professionalCategoryRepository = $professionalCategoryRepository;
        $this->roleRepository = $roleRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @throws InvalidStringValue
     * @throws NotFound
     */
    public function create(
        string $linkId,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $civility = CivilityEnum::MISTER_CODE,
        ?string $address = null,
        ?string $linkedin = null,
        ?string $function = null,
        ?string $seniorityDate = null,
        ?string $previousFunction = null,
        ?string $service = null,
        ?string $birthDate = null,
        ?string $professionalCategory = null,
        ?string $annualCompensation = null,
        ?string $userCodePostal = null,
        ?string $userDepartment = null,
        ?string $userCity = null,
        ?string $workMode = null
    ): User {
        $program = $this->programRepository->mustFindOneByLinkId($linkId);
        $typeCandidate = $this->userTypeRepository->mustFindOneById(UserTypeEnum::CANDIDATE);

        $user = new User($typeCandidate, $firstName, $lastName, $email, $phone);
        $user->setCivility($civility);
        $user->setAddress($address);
        $user->setLinkedin($linkedin);
        $user->setFunction($function);
        $user->setSeniorityDate($seniorityDate !== null ? new DateTimeImmutable($seniorityDate) : null);
        $user->setPreviousFunction($previousFunction);
        $user->setService($service);
        $user->setBirthDate($birthDate !== null ? new DateTimeImmutable($birthDate) : null);
        $user->setAnnualCompensation($annualCompensation);
        $user->setUserCodePostal($userCodePostal);
        $user->setUserDepartment($userDepartment);
        $user->setUserCity($userCity);
        $user->setStatus(false);
        $user->setAppointmentBooked(false);
        $user->setHasBeenTransferred(false);
        $user->setWorkMode($workMode);

        $company = $program->getCompany();
        if (! empty($company)) {
            $user->setCompany($company);
        }

        $roleCandidate = $this->roleRepository->findOneByName(RoleEnum::CANDIDATE_RDV);

        if (! empty($roleCandidate)) {
            $user->addRoleByUsersRoles($roleCandidate);
        }

        if (! empty($professionalCategory)) {
            $user->setProfessionalCategory($this->professionalCategoryRepository->mustFindOneById($professionalCategory));
        }

        $existingUser = $this->userRepository->findOneByEmail($user->getEmail());
        if ($existingUser) {
            $this->existingUserFromOfflineFormNotifier->notify($user, $existingUser->getId());

            return $user;
        }

        $this->userRepository->saveNoLog($user);

        $program->addUserByProgramsUsers($user);
        $this->programRepository->saveNoLog($program);

        return $user;
    }
}
