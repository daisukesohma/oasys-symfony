<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramOutplacement;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\UserRepository;

final class CreateProgramOutplacement
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
     * @param string[] $userIds
     * @param string[] $coachIds
     *
     * @throws NotFound
     */
    public function create(
        string $name,
        string $description,
        array $userIds,
        ?array $coachIds = [],
        ?string $modelId = null,
        ?int $period = null,
        ?string $companyId = null,
        bool $endSupportEmail = true
    ): ProgramOutplacement {
        $program = new ProgramOutplacement($name, $description, ProgramTypeEnum::OUTPLACEMENT);
        $this->createProgram->create(
            $name,
            $description,
            ProgramTypeEnum::OUTPLACEMENT,
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
