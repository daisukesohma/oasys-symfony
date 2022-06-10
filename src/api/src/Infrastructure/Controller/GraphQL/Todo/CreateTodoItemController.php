<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Todo;

use App\Application\Todo\CreateTodoItem;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class CreateTodoItemController extends AbstractController
{
    private CreateTodoItem $createTodoItem;

    public function __construct(CreateTodoItem $createTodoItem)
    {
        $this->createTodoItem = $createTodoItem;
    }

    /**
     * @throws NotFound
     *
     * @Logged
     * @Mutation
     */
    public function createTodoItem(string $label, string $programId, ?string $userId = null): Todo
    {
        return $this->createTodoItem->create($label, $programId, $userId);
    }
}
