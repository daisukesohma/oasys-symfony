<?php

declare(strict_types=1);

namespace App\Application\Todo;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Todo;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\TodoRepository;
use App\Domain\Repository\UserRepository;

final class CreateTodoItem
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
     * @throws NotFound
     */
    public function create(string $label, string $programId, ?string $userId = null): Todo
    {
        $program = $this->programRepository->mustFindOneById($programId);

        $todo = new Todo($program, $label);
        if (! empty($userId)) {
            $todo->setUser($this->userRepository->mustFindOneById($userId));
        }

        $this->todoRepository->save($todo);

        return $todo;
    }
}
