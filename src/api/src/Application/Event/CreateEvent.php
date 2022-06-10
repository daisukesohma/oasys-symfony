<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\Program\AddEventToProgram;
use App\Application\Queue\QueueMessagingManager;
use App\Domain\Enum\EventMeetingEnum;
use App\Domain\Enum\EventStatusEnum;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\EventExistsAtTimeException;
use App\Domain\Exception\InvalidData;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventModelRepository;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\UserRepository;
use Safe\DateTimeImmutable;
use Safe\Exceptions\DatetimeException;
use function count;
use function time;

final class CreateEvent
{
    private EventRepository $eventRepository;
    private EventModelRepository $eventModelRepository;
    private UserRepository $userRepository;
    private AddEventToProgram $addEventToProgram;
    private QueueMessagingManager $queueMessagingManager;
    private EventPresentialNotifier $eventPresentialNotifier;
    private EventVisioNotifier $eventVisioNotifier;

    public function __construct(
        EventRepository $eventRepository,
        EventModelRepository $eventModelRepository,
        UserRepository $userRepository,
        AddEventToProgram $addEventToProgram,
        QueueMessagingManager $queueMessagingManager,
        EventPresentialNotifier $eventPresentialNotifier,
        EventVisioNotifier $eventVisioNotifier
    ) {
        $this->eventRepository = $eventRepository;
        $this->eventModelRepository = $eventModelRepository;
        $this->userRepository = $userRepository;
        $this->addEventToProgram = $addEventToProgram;
        $this->queueMessagingManager = $queueMessagingManager;
        $this->eventPresentialNotifier = $eventPresentialNotifier;
        $this->eventVisioNotifier = $eventVisioNotifier;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidDateValue
     * @throws InvalidData
     * @throws EventExistsAtTimeException
     */
    public function create(
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
        ?int $numberMaxInvites = null,
        bool $isDraft = false
    ): Event {
        $event = new Event($name, $description, $type);
        $event->setTeamsLink($teamsLink);
        $event->setStatus(EventStatusEnum::CREATED);
        $event->setMeetingPlace($meetingPlace);
        $event->setMeetingRoom($meetingRoom);
        $event->setEvaluationSurvey($evaluationSurvey);
        $event->setNumberMaxInvites($numberMaxInvites ?? 0);

        if ($event->getType() === EventTypeEnum::INDIVIDUAL_SESSION && count($userIds) > 1 && ! $isDraft) {
            throw new InvalidData('There should be only one user for individual session event');
        }

        if (! empty($modelId)) {
            $eventModel = $this->eventModelRepository->mustFindOneById($modelId);
            $event->setEventModel($eventModel);
        }

        $organizer = null;
        if (! empty($organizerId)) {
            $organizer = $this->userRepository->mustFindOneById($organizerId);
            $event->setOrganizer($organizer);
        }

        $userBeans = [];
        foreach ($userIds as $user) {
            $userBeans[] = $this->userRepository->mustFindOneById($user);
        }
        $event->setUsers($userBeans);

        $isPastEvent = false;
        if (! empty($dateEvent)) {
            try {
                $dateEvent = new DateTimeImmutable($dateEvent);
                $allowPastDates = $this->userRepository->getLoggedUser()->getType()->getId() === UserTypeEnum::ADMINISTRATOR;
                $isPastEvent = $dateEvent->getTimestamp() < time();

                $event->setDateEvent($dateEvent, $allowPastDates);
                if ($dateEventEnd === null) {
                    $dateEventEnd = $dateEvent->modify('+1 hour');
                } else {
                    $dateEventEnd = new DateTimeImmutable($dateEventEnd);
                }

                $event->setDateEventEnd($dateEventEnd, $allowPastDates);
                if (! $isDraft && ! empty($userIds)) {
                    $event->setStatus(EventStatusEnum::UPCOMING);
                }

                if ($organizer !== null) {
                    $existingEvent = $this->eventRepository->getEventBetween($dateEvent, $dateEventEnd, $organizer);
                    if ($existingEvent !== null) {
                        throw new EventExistsAtTimeException($existingEvent->getName());
                    }
                    //associate the coach to the invited user when event is created with users directly
                    if ($event->getType() === EventTypeEnum::INDIVIDUAL_SESSION && count($userIds)) {
                        if ($userBeans[0]->getCoach() === null) {
                            $userBeans[0]->setCoach($organizer);
                        }
                    }
                }
                if ($allowPastDates && $isPastEvent) {
                    $event->setStatus(EventStatusEnum::FINISHED);
                }
            } catch (DatetimeException $e) {
                throw new InvalidDateValue('Input dateEvent is in wrong format', 400, $e);
            }
        }

        $this->eventRepository->save($event);

        if (! empty($programId)) {
            $this->addEventToProgram->add($programId, $event->getId());
        }

        if (! empty($dateEvent) && ! $isDraft && ! $isPastEvent) {
            $this->queueMessagingManager->queue($event->getId());

            foreach ($event->getUsers() as $user) {
                if ($event->getMeetingPlace() === EventMeetingEnum::PRESENTIAL) {
                    $this->eventPresentialNotifier->notify($event, $user);
                } else {
                    $this->eventVisioNotifier->notify($event, $user);
                }
            }
        }

        return $event;
    }
}
