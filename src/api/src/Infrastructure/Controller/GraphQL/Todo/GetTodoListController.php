<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Todo;

use App\Application\Todo\GetTodoList;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetTodoListController extends AbstractController
{
    private GetTodoList $getTodoList;

    public function __construct(GetTodoList $getTodoList)
    {
        $this->getTodoList = $getTodoList;
    }

    /**
     * @return Todo[]
     *
     * @throws NotFound
     *
     * @Logged
     * @Query
     */
    public function getTodosForProgram(string $programId, ?string $userId = null): array
    {
        return $this->getTodoList->get($programId, $userId);
    }
}
