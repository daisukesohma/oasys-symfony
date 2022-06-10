<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\UpdateEventMemo;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class UpdateEventMemoController extends AbstractController
{
    private UpdateEventMemo $updateEventMemo;

    public function __construct(UpdateEventMemo $updateEventMemo)
    {
        $this->updateEventMemo = $updateEventMemo;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     */
    public function updateEventMemo(string $id, string $memo): Event
    {
        return $this->updateEventMemo->update($id, $memo);
    }
}
