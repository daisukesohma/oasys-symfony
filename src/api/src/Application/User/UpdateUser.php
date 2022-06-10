<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Application\File\DeleteFile;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\Exist;
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

final class UpdateUser
{
    private UserRepository $userRepository;
    private UpdateEmail $updateEmailAddress;
    private UserTypeRepository $userTypeRepository;
    private CompanyRepository $companyRepository;
    private FileDescriptorRepository $fileDescriptorRepository;
    private RoleRepository $roleRepository;
    private DeleteFile $deleteFile;
    private CoachSpecialityRepository $coachSpecialityRepository;
    private ProfessionalCategoryRepository $professionalCategoryRepository;

    public function __construct(UserRepository $userRepository, UpdateEmail $updateEmailAddress, UserTypeRepository $userTypeRepository, CompanyRepository $companyRepository, FileDescriptorRepository $fileDescriptorRepository, RoleRepository $roleRepository, DeleteFile $deleteFile, CoachSpecialityRepository $coachSpecialityRepository, ProfessionalCategoryRepository $professionalCategoryRepository)
    {
        $this->userRepository = $userRepository;
        $this->updateEmailAddress = $updateEmailAddress;
        $this->userTypeRepository = $userTypeRepository;
        $this->companyRepository = $companyRepository;
        $this->fileDescriptorRepository = $fileDescriptorRepository;
        $this->roleRepository = $roleRepository;
        $this->deleteFile = $deleteFile;
        $this->coachSpecialityRepository = $coachSpecialityRepository;
        $this->professionalCategoryRepository = $professionalCategoryRepository;
    }

    /**
     * @param string[] $roleIds
     *
     * @throws InvalidStringValue
     * @throws NotFound
     * @throws Exist
     */
    public function updateUser(
        string $id,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $typeId,
        array $roleIds,
        string $rootPath,
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
        $user = $this->userRepository->mustFindOneById($id);
        if ($user->getEmail() !== $email) {
            $this->updateEmailAddress->updateEmail($user->getId(), $email);
        }

        $user->setCoach(! empty($coachId) ? $this->userRepository->mustFindOneById($coachId) : null);
        $user->setType($this->userTypeRepository->mustFindOneById($typeId));
        $user->setCompany(! empty($companyId) ? $this->companyRepository->mustFindOneById($companyId) : null);
        $user->setStatus($status);

        $deleteFile = null;
        if ($user->getProfilePicture() !== null && $profilePictureId !== $user->getProfilePicture()->getId()) {
            $deleteFile = $user->getProfilePicture()->getId();
        }

        $user->setUserInformation(
            $user,
            $firstName,
            $lastName,
            $phone,
            $civility,
            $address,
            $linkedin,
            $function,
            $seniorityDate,
            $previousFunction,
            ! empty($profilePictureId) ? $this->fileDescriptorRepository->mustFindOneById($profilePictureId) : null
        );
        $user->setCvFile(! empty($cvFileId) ? $this->fileDescriptorRepository->mustFindOneById($cvFileId) : null);
        $user->setUserCodePostal($userCodePostal);
        $user->setUserDepartment($userDepartment);
        $user->setUserCity($userCity);
        $user->setWorkMode($workMode);

        if ($typeId === UserTypeEnum::CANDIDATE) {
            $user->setNFirstName($nFirstName);
            $user->setNLastName($nLastName);
            $user->setNEmail($nEmail);
            $user->setNPhone($nPhone);
            $user->setService($service);
            $user->setVille($ville);
            $user->setDepartment($department);
            $user->setPostCode($postCode);
            $user->setAnnualCompensation($annualCompensation);
            $user->setProfessionalCategory($professionalCategory ? $this->professionalCategoryRepository->mustFindOneById($professionalCategory) : null);
            $user->setBirthDate($birthDate !== null ? new DateTimeImmutable($birthDate) : null);
        } elseif ($typeId === UserTypeEnum::COACH) {
            $user->setCoachSpeciality($coachSpeciality ? $this->coachSpecialityRepository->mustFindOneById($coachSpeciality) : null);
        }

        $roles = [];
        foreach ($roleIds as $roleId) {
            $roles[] = $this->roleRepository->mustFindOneById($roleId);
        }

        $user->setRolesByUsersRoles($roles);
        $this->userRepository->save($user);

        if (! empty($deleteFile)) {
            $this->deleteFile->delete($deleteFile, $rootPath);
        }

        return $user;
    }
}
