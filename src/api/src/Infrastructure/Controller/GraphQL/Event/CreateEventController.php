<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\CreateEvent;
use App\Domain\Exception\EventExistsAtTimeException;
use App\Domain\Exception\InvalidData;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateEventController extends AbstractController
{
    private CreateEvent $createEvent;

    public function __construct(CreateEvent $createEvent)
    {
        $this->createEvent = $createEvent;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidDateValue
     * @throws InvalidData
     * @throws EventExistsAtTimeException
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_EVENT")
     */
    public function createEvent(
        string $name,
        string $description,
        string $type,
        array $userIds,
        ?string $organizerId = null,
        ?string $dateEvent = null,
        ?string $dateEventEnd = null,
        ?string $modelId = null,
        ?string $programId = null,
        ?string $teamsLink = null,
        ?string $meetingPlace = null,
        ?string $meetingRoom = null,
        ?string $evaluationSurvey = null,
        ?int $numberMaxInvites = null
    ): Event {
        return $this->createEvent->create(
            $name,
            $description,
            $type,
            $userIds,
            $organizerId,
            $dateEvent,
            $dateEventEnd,
            $modelId,
            $programId,
            $teamsLink,
            $meetingPlace,
            $meetingRoom,
            $evaluationSurvey,
            $numberMaxInvites
        );
    }
}
