<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\File\DeleteFile;
use App\Application\Program\AddEventToProgram;
use App\Application\Program\UpdateProgramEventDates;
use App\Application\Queue\QueueMessagingManager;
use App\Domain\Enum\EventMeetingEnum;
use App\Domain\Enum\EventStatusEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\EventExistsAtTimeException;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventModelRepository;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\UserRepository;
use Safe\DateTimeImmutable;
use Safe\Exceptions\DatetimeException;
use function time;

final class UpdateEvent
{
    private EventRepository $eventRepository;
    private EventModelRepository $eventModelRepository;
    private UserRepository $userRepository;
    private QueueMessagingManager $queueMessagingManager;
    private EventPresentialNotifier $eventPresentialNotifier;
    private EventVisioNotifier $eventVisioNotifier;
    private EventDateChangeNotifier $eventDateChangeNotifier;
    private AddEventToProgram $addEventToProgram;
    private DeleteFile $deleteFile;
    private UpdateProgramEventDates $updateProgramEventDates;

    public function __construct(
        EventRepository $eventRepository,
        EventModelRepository $eventModelRepository,
        UserRepository $userRepository,
        QueueMessagingManager $queueMessagingManager,
        EventPresentialNotifier $eventPresentialNotifier,
        EventVisioNotifier $eventVisioNotifier,
        EventDateChangeNotifier $eventDateChangeNotifier,
        AddEventToProgram $addEventToProgram,
        DeleteFile $deleteFile,
        UpdateProgramEventDates $updateProgramEventDates
    ) {
        $this->eventRepository = $eventRepository;
        $this->eventModelRepository = $eventModelRepository;
        $this->userRepository = $userRepository;
        $this->queueMessagingManager = $queueMessagingManager;
        $this->eventPresentialNotifier = $eventPresentialNotifier;
        $this->eventVisioNotifier = $eventVisioNotifier;
        $this->eventDateChangeNotifier = $eventDateChangeNotifier;
        $this->addEventToProgram = $addEventToProgram;
        $this->deleteFile = $deleteFile;
        $this->updateProgramEventDates = $updateProgramEventDates;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidDateValue
     * @throws InvalidStringValue
     * @throws EventExistsAtTimeException
     */
    public function update(
        string $id,
        string $name,
        string $description,
        string $type,
        array $userIds,
        string $rootPath,
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
        $notifyNewEvent = false;
        $previousDateEvent = null;

        $event = $this->eventRepository->mustFindOneById($id);

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

        $event->setName($name);
        $event->setDescription($description);
        $event->setType($type);
        $event->setTeamsLink($teamsLink);
        $event->setMeetingPlace($meetingPlace);
        $event->setMeetingRoom($meetingRoom);
        $event->setEvaluationSurvey($evaluationSurvey);
        $event->setNumberMaxInvites($numberMaxInvites ?? 0);

        if (! empty($dateEvent) && ! empty($dateEventEnd)) {
            try {
                $dateEvent = new DateTimeImmutable($dateEvent);
                $allowPastDates = $this->userRepository->getLoggedUser()->getType()->getId() === UserTypeEnum::ADMINISTRATOR;
                $isPastEvent = $dateEvent->getTimestamp() < time();

                if ($event->getDateEvent() !== null && $dateEvent->getTimestamp() !== $event->getDateEvent()->getTimestamp()) {
                    $previousDateEvent = $event->getDateEvent();
                }

                $event->setDateEvent($dateEvent, $allowPastDates);

                $dateEventEnd = new DateTimeImmutable($dateEventEnd);
                $event->setDateEventEnd($dateEventEnd, $allowPastDates);

                if ($event->getStatus() === EventStatusEnum::CREATED && ! $isPastEvent) {
                    $notifyNewEvent = true;
                }

                if (! empty($userIds) && $event->getStatus() !== EventStatusEnum::FINISHED) {
                    $event->setStatus(EventStatusEnum::UPCOMING);
                } else {
                    if (empty($userIds)) {
                        $event->setStatus(EventStatusEnum::CREATED);
                    }
                }
                $this->eventRepository->save($event);

                if ($organizer !== null) {
                    $existingEvent = $this->eventRepository->getEventBetween($dateEvent, $dateEventEnd, $organizer);
                    if ($existingEvent !== null && $existingEvent->getId() !== $event->getId()) {
                        throw new EventExistsAtTimeException($existingEvent->getName());
                    }
                }
            } catch (DatetimeException $e) {
                throw new InvalidDateValue('Input dateEvent is in wrong format', 400, $e);
            }
        }

        $this->eventRepository->save($event);

        if (! empty($dateEvent) && ! empty($dateEventEnd)) {
            $this->queueMessagingManager->queue($event->getId());
        }

        // Call AddEventToProgram to make sure program's start and end date are updated
        if (! empty($programId)) {
            $oldProgram = $event->getProgram() !== null ? $event->getProgram()->getId() : null;
            $this->addEventToProgram->add($programId, $event->getId());

            if ($oldProgram !== null) {
                $this->updateProgramEventDates->update($oldProgram);
            }
        } elseif ($event->getProgram() !== null) {
            $this->addEventToProgram->add($event->getProgram()->getId(), $event->getId());
        }

        if ($notifyNewEvent) {
            foreach ($event->getUsers() as $user) {
                if ($event->getMeetingPlace() === EventMeetingEnum::PRESENTIAL) {
                    $this->eventPresentialNotifier->notify($event, $user);
                } else {
                    $this->eventVisioNotifier->notify($event, $user);
                }
            }
        }

        if ($previousDateEvent !== null) {
            foreach ($event->getUsers() as $user) {
                $this->eventDateChangeNotifier->notify($event, $user, $previousDateEvent);
            }
        }

        return $event;
    }
}
