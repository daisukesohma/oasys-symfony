<?php

declare(strict_types=1);

namespace App\Application\Todo;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Todo;
use App\Domain\Repository\TodoRepository;

final class MarkTodoAsDone
{
    private TodoRepository $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    /**
     * @throws NotFound
     */
    public function mark(string $todoId): Todo
    {
        $todo = $this->todoRepository->mustFindOneById($todoId);
        $todo->setDone(true);
        $this->todoRepository->save($todo);

        return $todo;
    }
}
