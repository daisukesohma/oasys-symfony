<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Model\User;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\ProgramCoachingIndividualRepository;
use App\Domain\Repository\ProgramModelRepository;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\UserRepository;
use Safe\DateTimeImmutable;
use function array_map;

final class UpdateProgram
{
    private ProgramRepository $programRepository;
    private ProgramModelRepository $programModelRepository;
    private UserRepository $userRepository;
    private ProgramCoachingIndividualRepository $programCoachingIndividualRepository;
    private CompanyRepository $companyRepository;

    public function __construct(ProgramRepository $programRepository, ProgramModelRepository $programModelRepository, UserRepository $userRepository, ProgramCoachingIndividualRepository $programCoachingIndividualRepository, CompanyRepository $companyRepository)
    {
        $this->programRepository = $programRepository;
        $this->programModelRepository = $programModelRepository;
        $this->userRepository = $userRepository;
        $this->programCoachingIndividualRepository = $programCoachingIndividualRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param string[] $userIds
     * @param string[] $coachIds
     *
     * @throws NotFound
     * @throws InvalidStringValue
     */
    public function update(
        string $id,
        string $name,
        string $description,
        string $type,
        array $userIds,
        ?array $coachIds = [],
        ?string $modelId = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $dateStart = null,
        ?string $dateEnd = null,
        ?int $period = null,
        ?string $companyId = null,
        bool $endSupportEmail = true
    ): Program {
        if ($type === ProgramTypeEnum::INDIVIDUAL && $firstName && $lastName && $email && $phone) {
            $programIndividual = $this->programCoachingIndividualRepository->mustFindOneById($id);
            $programIndividual->setFirstName($firstName);
            $programIndividual->setLastName($lastName);
            $programIndividual->setEmail($email);
            $programIndividual->setPhone($phone);
            $this->programCoachingIndividualRepository->save($programIndividual);
        }
        $program = $this->programRepository->mustFindOneById($id);
        if (! empty($modelId)) {
            $programModel = $this->programModelRepository->mustFindOneById($modelId);
            $program->setProgramModel($programModel);
        } else {
            $program->setProgramModel(null);
        }

        $program->setName($name);
        $program->setDescription($description);
        $program->setType($type);
        $program->setPeriod($period);

        $userBeans = [];
        foreach ($userIds as $user) {
            $userBeans[] = $this->userRepository->mustFindOneById($user);
        }
        $program->setUsersByProgramsUsers($userBeans);

        $currentUser = $this->userRepository->getLoggedUser();
        if ($currentUser->getType()->getId() === UserTypeEnum::ADMINISTRATOR && ! empty($coachIds)) {
            /** @var User[] $coaches */
            $coaches = array_map(fn(string $coachId) => $this->userRepository->mustFindOneById($coachId), $coachIds);
            $program->setUsersByProgramsCoaches($coaches);
        }

        if (! empty($dateStart)) {
            $program->setDateStart(new DateTimeImmutable($dateStart));
        }

        if ($program->getDateStart() !== null && $program->getPeriod() !== null) {
            $program->setDateEnd($program->getDateStart()->modify('+' . $program->getPeriod() . ' months'));
        }

        $program->setCompany($companyId === null ? null : $this->companyRepository->mustFindOneById($companyId));
        $program->setEndSupportEmail($endSupportEmail);

        $this->programRepository->save($program);

        return $program;
    }
}
