<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\DeleteEvent;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class DeleteEventController extends AbstractController
{
    private DeleteEvent $deleteEvent;

    public function __construct(DeleteEvent $deleteEvent)
    {
        $this->deleteEvent = $deleteEvent;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     */
    public function deleteEvent(string $eventId): Event
    {
        return $this->deleteEvent->delete($eventId);
    }
}
