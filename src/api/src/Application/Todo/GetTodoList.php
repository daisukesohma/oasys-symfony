<?php

declare(strict_types=1);

namespace App\Application\Todo;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Todo;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\TodoRepository;
use App\Domain\Repository\UserRepository;

final class GetTodoList
{
    private TodoRepository $todoRepository;
    private ProgramRepository $programRepository;
    private UserRepository $userRepository;

    public function __construct(TodoRepository $todoRepository, ProgramRepository $programRepository, UserRepository $userRepository)
    {
        $this->todoRepository = $todoRepository;
        $this->programRepository = $programRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return Todo[]
     *
     * @throws NotFound
     */
    public function get(string $programId, ?string $userId = null): array
    {
        $program = $this->programRepository->mustFindOneById($programId);
        $user = $userId === null ? null : $this->userRepository->mustFindOneById($userId);

        return $this->todoRepository->findByProgram($program, $user);
    }
}
