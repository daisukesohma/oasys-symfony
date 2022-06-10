<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\RateEvent;
use App\Domain\Exception\NotFound;
use App\Domain\Model\EventRate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class RateEventController extends AbstractController
{
    private RateEvent $rateEvent;

    public function __construct(RateEvent $rateEvent)
    {
        $this->rateEvent = $rateEvent;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     */
    public function rateEvent(string $eventId, int $starsNumber, string $comment): EventRate
    {
        return $this->rateEvent->rate($eventId, $starsNumber, $comment);
    }
}
