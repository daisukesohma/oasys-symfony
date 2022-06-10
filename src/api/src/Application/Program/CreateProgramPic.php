<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Model\ProgramPic;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\UserRepository;
use Ramsey\Uuid\Uuid;

final class CreateProgramPic
{
    private ProgramRepository $programRepository;
    private CreateProgram $createProgram;
    private UserRepository $userRepository;

    public function __construct(ProgramRepository $programRepository, CreateProgram $createProgram, UserRepository $userRepository)
    {
        $this->programRepository = $programRepository;
        $this->createProgram = $createProgram;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string[] $coachIds
     *
     * @throws NotFound
     */
    public function create(
        string $name,
        string $description,
        array $coachIds,
        ?string $modelId = null,
        ?int $period = null,
        ?string $companyId = null,
        bool $endSupportEmail = true,
        int $appointmentTimeLimit = 0,
        ?string $inscriptionText = null
    ): Program {
        $program = new ProgramPic($name, $description, ProgramTypeEnum::PIC);
        $program->setAppointmentTimeLimit($appointmentTimeLimit);
        $program->setInscriptionText($inscriptionText);
        if ($program->getLinkId() === null) {
            $program->setLinkId(Uuid::uuid4()->toString());
        }

        $this->createProgram->create(
            $name,
            $description,
            ProgramTypeEnum::PIC,
            [],
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
