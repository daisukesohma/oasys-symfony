<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Todo;

use App\Application\Todo\MarkTodoAsDone;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class MarkTodoAsDoneController extends AbstractController
{
    private MarkTodoAsDone $markTodoAsDone;

    public function __construct(MarkTodoAsDone $markTodoAsDone)
    {
        $this->markTodoAsDone = $markTodoAsDone;
    }

    /**
     * @throws NotFound
     *
     * @Logged
     * @Mutation
     */
    public function markTodoAsDone(string $todoId): Todo
    {
        return $this->markTodoAsDone->mark($todoId);
    }
}
