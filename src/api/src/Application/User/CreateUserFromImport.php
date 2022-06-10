<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidImportUser;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use App\Domain\Model\Role;
use App\Domain\Model\User;
use App\Domain\Model\UserType;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\ProfessionalCategoryRepository;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use Safe\DateTimeImmutable;
use function Safe\ini_set;
use function Safe\sprintf;
use function strtolower;
use function trim;

final class CreateUserFromImport
{
    private UserRepository $userRepository;
    private UserTypeRepository $userTypeRepository;
    private CompanyRepository $companyRepository;
    private RoleRepository $roleRepository;
    private ProfessionalCategoryRepository $professionalCategoryRepository;

    private const MODEL_MAP = [
        Role::class => 'Le rôle que vous avez entré n\'existe pas',
        User::class => 'Le coach référent que vous avez entré n\'existe pas',
        Company::class => 'L\'entreprise que vous avez entré n\'existe pas',
    ];

    private const ERROR_MAP = [
        'type' => 'Le champ Type est obligatoire',
        'firstName' => 'Le champ Prénom est obligatoire',
        'lastName' => 'Le champ Nom est obligatoire',
        'email' => 'Le format de l\'email est invalide',
        'required_email' => 'L\'email existe déjà',
        'duplicate_email' => 'L\'utilisateur avec l\'e-mail %s existe déjà',
        'phone' => 'Le numéro de téléphone est dans un format invalide',
        'required_phone' => 'Le champ Téléphone est obligatoire',
        'roles' => 'Au moins un rôle doit être sélectionné',
        'civility' => 'La civilité saisie n\'est pas valide',
        'programType' => 'Le type de prestation est obligatoire pour un candidat',
        'nFirstName' => 'Le champ Prénom du N+1 est obligatoire',
        'nLastName' => 'Le champ Nom du N+1 est obligatoire',
        'nEmail' => 'Le champ Email du N+1 est obligatoire',
        'nPhone' => 'Le champ Télephone du N+1 est obligatoire',
        'programCompany' => 'L\'entreprise doit être %s',
    ];

    /** @var Role[] */
    private static array $cachedRoles = [];
    /** @var User[] */
    private static array $cachedUsers = [];
    /** @var Company[] */
    private static array $cachedCompanies = [];
    /** @var UserType[] */
    private static array $cachedUserTypes = [];
    private static ?User $cachedLoggedUser = null;

    public function __construct(
        UserRepository $userRepository,
        UserTypeRepository $userTypeRepository,
        CompanyRepository $companyRepository,
        RoleRepository $roleRepository,
        ProfessionalCategoryRepository $professionalCategoryRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userTypeRepository = $userTypeRepository;
        $this->companyRepository = $companyRepository;
        $this->roleRepository = $roleRepository;
        $this->professionalCategoryRepository = $professionalCategoryRepository;
    }

    /**
     * @param mixed[] $row
     *
     * @throws InvalidStringValue
     * @throws NotFound
     */
    public function import(array $row, bool $save = true, bool $validateCoach = true, bool $programPicImport = false, ?Company $validateCompany = null): ?User
    {
        ini_set('max_execution_time', '0');
        $errors = [];
        if (! $save && ! $this->userRepository->checkEmailUnique($row['email'])) {
            $errors[] = sprintf(self::ERROR_MAP['duplicate_email'], $row['email']);
        }

        try {
            User::validateFirstName($row['firstName']);
        } catch (InvalidStringValue $e) {
            $errors[] = self::ERROR_MAP['firstName'];
        }
        if (isset(UserTypeEnum::values()[$row['type']])) {
            $row['type'] = UserTypeEnum::values()[$row['type']];
        }

        try {
            User::validateLastName($row['lastName']);
        } catch (InvalidStringValue $e) {
            $errors[] = self::ERROR_MAP['lastName'];
        }

        try {
            User::validateEmail($row['email']);
        } catch (InvalidStringValue $e) {
            $errors[] = empty($row['email']) ? self::ERROR_MAP['required_email'] : self::ERROR_MAP['email'];
        }

        if (! empty($row['phone']) || $row['type'] !== UserTypeEnum::CANDIDATE) {
            try {
                User::validatePhone($row['phone']);
            } catch (InvalidStringValue $e) {
                $errors[] = empty($row['phone']) ? self::ERROR_MAP['required_phone'] : self::ERROR_MAP['phone'];
            }
        }

        try {
            User::validateCivility($row['civility']);
        } catch (InvalidStringValue $e) {
            $errors[] = self::ERROR_MAP['civility'];
        }

        if ($row['type'] === UserTypeEnum::CANDIDATE) {
            try {
                if (! empty($row['nEmail'])) {
                    User::validateEmail($row['nEmail']);
                }
            } catch (InvalidStringValue $e) {
                $errors[] = self::ERROR_MAP['nEmail'];
            }

            try {
                if (! empty($row['nPhone'])) {
                    User::validatePhone($row['nPhone']);
                }
            } catch (InvalidStringValue $e) {
                $errors[] = self::ERROR_MAP['nPhone'];
            }
        }

        /** @var Role[] $roles */
        $roles = [];
        if (empty($row['roles'])) {
            $errors[] = self::ERROR_MAP['roles'];
        }
        foreach ($row['roles'] as $k => $roleName) {
            if (isset(self::$cachedRoles[trim($roleName)])) {
                $roles[] = self::$cachedRoles[trim($roleName)];
            } else {
                $role = $this->roleRepository->findOneByName(trim($roleName));
                if ($role === null) {
                    $errors[] = self::MODEL_MAP[Role::class];
                } else {
                    $roles[] = $role;
                    self::$cachedRoles[trim($roleName)] = $role;
                }
            }
        }

        $company = null;
        $coach = null;
        $programType = null;
        if ($row['type'] === UserTypeEnum::CANDIDATE) {
            if (isset(self::$cachedCompanies[$row['company']])) {
                $company = self::$cachedCompanies[$row['company']];
            } else {
                $company = $this->companyRepository->findOneByName($row['company']);
                if ($company === null) {
                    $errors[] = self::MODEL_MAP[Company::class];
                } else {
                    self::$cachedCompanies[$row['company']] = $company;
                }
            }

            if ($validateCompany !== null && ($company === null || $company->getId() !== $validateCompany->getId())) {
                $errors[] = sprintf(self::ERROR_MAP['programCompany'], $validateCompany->getName());
            }

            if (! empty($row['programType'])) {
                $programType = $row['programType'];
            } else {
                $errors[] = self::ERROR_MAP['programType'];
            }

            if ($validateCoach) {
                if (isset(self::$cachedUsers[$row['coach']])) {
                    $coach = self::$cachedUsers[$row['coach']];
                } else {
                    $coach = $this->userRepository->findOneByEmail($row['coach']);
                    if ($coach === null) {
                        $errors[] = self::MODEL_MAP[User::class];
                    } else {
                        self::$cachedUsers[$row['coach']] = $coach;
                    }
                }
            }
        }

        if (! isset(self::$cachedUserTypes[$row['type']])) {
            try {
                self::$cachedUserTypes[$row['type']] = $this->userTypeRepository->mustFindOneById($row['type']);
            } catch (NotFound $e) {
                $errors[] = self::ERROR_MAP['type'];
            }
        }

        if (! empty($errors)) {
            throw new InvalidImportUser($errors);
        }

        $user = null;
        if ($save) {
            if (self::$cachedLoggedUser === null) {
                self::$cachedLoggedUser = $this->userRepository->getLoggedUser();
            }

            $user = new User(self::$cachedUserTypes[$row['type']], $row['firstName'], $row['lastName'], $row['email'], $row['phone']);
            $user->setRolesByUsersRoles($roles);
            $user->setStatus(strtolower($row['status']) === 'oui');
            $user->setCivility($row['civility']);
            $user->setAddress($row['address']);
            $user->setLinkedin($row['linkedIn']);
            $user->setFunction($row['function']);
            $user->setPreviousFunction($row['previousFunction']);
            $user->setSeniorityDate($row['seniorityDate'] ? new DateTimeImmutable($row['seniorityDate']) : null);
            if (isset($row['birthDate'])) {
                $user->setBirthDate($row['birthDate'] ? new DateTimeImmutable($row['birthDate']) : null);
            }
            $user->setCreatedAt(new DateTimeImmutable());
            $user->setCreatedBy(self::$cachedLoggedUser);
            $user->setUserCodePostal($row['userCodePostal']);
            $user->setUserDepartment($row['userDepartment']);
            $user->setUserCity($row['userCity']);

            if ($row['type'] === UserTypeEnum::CANDIDATE) {
                $user->setCoach($coach);
                $user->setCompany($company);
                $user->setProgramType($programType);
                $user->setNFirstName($row['nFirstName']);
                $user->setNLastName($row['nLastName']);
                $user->setNEmail($row['nEmail']);
                $user->setNPhone($row['nPhone']);
                if (! empty($row['service'])) {
                    $user->setService($row['service']);
                    $user->setVille($row['ville']);
                    $user->setDepartment($row['department']);
                    $user->setPostCode($row['postCode']);
                }
                if (! empty($row['professionalCategory'])) {
                    $user->setProfessionalCategory($this->professionalCategoryRepository->mustFindOneByIdOrLabel($row['professionalCategory']));
                }
                if (! empty($row['annualCompensation'])) {
                    $user->setAnnualCompensation($row['annualCompensation']);
                }
                if ($programPicImport) {
                    $user->setAppointmentBooked(false);
                }
            }

            $this->userRepository->saveNoLog($user);
        }

        return $user;
    }
}
