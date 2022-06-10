<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramCoachingIndividual;
use App\Domain\Repository\ProgramCoachingIndividualRepository;
use App\Domain\Repository\UserRepository;

final class CreateProgramCoachingIndividual
{
    private CreateProgram $createProgram;
    private ProgramCoachingIndividualRepository $programCoachingIndividualRepository;
    private UserRepository $userRepository;

    public function __construct(CreateProgram $createProgram, UserRepository $userRepository, ProgramCoachingIndividualRepository $programCoachingIndividualRepository)
    {
        $this->createProgram = $createProgram;
        $this->programCoachingIndividualRepository = $programCoachingIndividualRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     */
    public function create(
        string $name,
        string $description,
        string $type,
        array $userIds,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        ?string $coachId = null,
        ?string $modelId = null,
        ?int $period = null,
        ?string $companyId = null,
        bool $endSupportEmail = true
    ): ProgramCoachingIndividual {
        $program = new ProgramCoachingIndividual($name, $description, $type, $firstName, $lastName, $email, $phone);
        $this->programCoachingIndividualRepository->save($program);

        $currentUser = $this->userRepository->getLoggedUser();
        /** @var string[] $coachIds */
        $coachIds = [$currentUser->getType()->getId() === UserTypeEnum::ADMINISTRATOR && ! empty($coachId) ? $coachId : $currentUser->getId()];
        $this->createProgram->create(
            $name,
            $description,
            $type,
            $userIds,
            $coachIds,
            $modelId,
            $period,
            $companyId,
            $endSupportEmail,
            $program,
        );

        return $program;
    }
}
