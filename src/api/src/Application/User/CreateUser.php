<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Enum\CivilityEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidArrayValue;
use App\Domain\Exception\InvalidData;
use App\Domain\Exception\InvalidRight;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\CoachSpecialityRepository;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\ProfessionalCategoryRepository;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use Safe\DateTimeImmutable;

final class CreateUser
{
    private UserRepository $userRepository;
    private UserTypeRepository $userTypeRepository;
    private CompanyRepository $companyRepository;
    private FileDescriptorRepository $fileDescriptorRepository;
    private RoleRepository $roleRepository;
    private ProfessionalCategoryRepository $professionalCategoryRepository;
    private CoachSpecialityRepository $coachSpecialityRepository;

    public function __construct(
        UserRepository $userRepository,
        UserTypeRepository $userTypeRepository,
        CompanyRepository $companyRepository,
        FileDescriptorRepository $fileDescriptorRepository,
        RoleRepository $roleRepository,
        ProfessionalCategoryRepository $professionalCategoryRepository,
        CoachSpecialityRepository $coachSpecialityRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userTypeRepository = $userTypeRepository;
        $this->companyRepository = $companyRepository;
        $this->fileDescriptorRepository = $fileDescriptorRepository;
        $this->roleRepository = $roleRepository;
        $this->professionalCategoryRepository = $professionalCategoryRepository;
        $this->coachSpecialityRepository = $coachSpecialityRepository;
    }

    /**
     * @param string[] $roleIds
     *
     * @throws InvalidArrayValue
     * @throws InvalidStringValue
     * @throws InvalidRight
     * @throws InvalidData
     * @throws Exist
     * @throws NotFound
     */
    public function create(
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
        ?string $programType = null,
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
        if (! $this->userRepository->checkEmailUnique($email)) {
            throw new Exist(User::class, [], true);
        }

        if ($typeId === UserTypeEnum::ADMINISTRATOR && $this->userRepository->getLoggedUser()->getType()->getId() !== UserTypeEnum::ADMINISTRATOR) {
            throw new InvalidRight();
        }

        if (empty($programType) && $typeId === UserTypeEnum::CANDIDATE) {
            throw new InvalidData('Program Type should be specified for candidate user');
        }

        $userType = $this->userTypeRepository->mustFindOneById($typeId);
        $user = new User($userType, $firstName, $lastName, $email, $phone);

        $roles = [];
        foreach ($roleIds as $roleId) {
            $roles[] = $this->roleRepository->mustFindOneById($roleId);
        }

        if (empty($roles)) {
            throw new InvalidArrayValue('Roles are required', 400);
        }

        $user->setProfilePicture(! empty($profilePictureId) ? $this->fileDescriptorRepository->mustFindOneById($profilePictureId) : null);
        $user->setRolesByUsersRoles($roles);
        $user->setStatus($status);
        $user->setCivility($civility ?? CivilityEnum::MISTER_CODE);
        $user->setAddress($address);
        $user->setLinkedin($linkedin);
        $user->setFunction($function);
        $user->setPreviousFunction($previousFunction);
        $user->setSeniorityDate($seniorityDate !== null ? new DateTimeImmutable($seniorityDate) : null);
        $user->setCvFile(! empty($cvFileId) ? $this->fileDescriptorRepository->mustFindOneById($cvFileId) : null);
        $user->setUserCodePostal($userCodePostal);
        $user->setUserDepartment($userDepartment);
        $user->setUserCity($userCity);
        $user->setWorkMode($workMode);

        $coach = null;
        if ($typeId === UserTypeEnum::CANDIDATE) {
            if ($coachId === null) {
                throw new NotFound(User::class, ['coachId' => null]);
            }
            $coach = $this->userRepository->mustFindOneById($coachId);
            $user->setCoach($coach);
            $user->setProgramType($programType);
            $user->setNFirstName($nFirstName);
            $user->setNLastName($nLastName);
            $user->setNEmail($nEmail);
            $user->setNPhone($nPhone);
            $user->setService($service);
            $user->setVille($ville);
            $user->setDepartment($department);
            $user->setPostCode($postCode);
            $user->setBirthDate($birthDate !== null ? new DateTimeImmutable($birthDate) : null);
            $user->setAnnualCompensation($annualCompensation);
            $user->setProfessionalCategory($professionalCategory ? $this->professionalCategoryRepository->mustFindOneById($professionalCategory) : null);
        } elseif ($typeId === UserTypeEnum::COACH) {
            $user->setCoachSpeciality($coachSpeciality ? $this->coachSpecialityRepository->mustFindOneById($coachSpeciality) : null);
        }

        if ($companyId !== null) {
            $company = $this->companyRepository->mustFindOneById($companyId);
            $user->setCompany($company);
        }

        $this->userRepository->save($user);

        return $user;
    }
}
