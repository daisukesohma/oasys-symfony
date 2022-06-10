<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\UpdateEvent;
use App\Domain\Exception\EventExistsAtTimeException;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Infrastructure\Config\EnvVarHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class UpdateEventController extends AbstractController
{
    private UpdateEvent $updateEvent;
    private EnvVarHelper $envVarHelper;

    public function __construct(UpdateEvent $updateEvent, EnvVarHelper $envVarHelper)
    {
        $this->updateEvent = $updateEvent;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidDateValue
     * @throws InvalidStringValue
     * @throws EventExistsAtTimeException
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_EVENT")
     */
    public function updateEvent(
        string $id,
        string $name,
        string $description,
        string $type,
        array $userIds,
        ?string $organizerId = null,
        ?string $dateEvent = null,
        ?string $dateEventEnd = null,
        ?string $modelId = null,
        ?string $teamsLink = null,
        ?string $meetingPlace = null,
        ?string $meetingRoom = null,
        ?string $evaluationSurvey = null,
        ?string $programId = null,
        ?int $numberMaxInvites = null
    ): Event {
        return $this->updateEvent->update(
            $id,
            $name,
            $description,
            $type,
            $userIds,
            $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH),
            $organizerId,
            $dateEvent,
            $dateEventEnd,
            $modelId,
            $teamsLink,
            $meetingPlace,
            $meetingRoom,
            $evaluationSurvey,
            $programId,
            $numberMaxInvites
        );
    }
}
