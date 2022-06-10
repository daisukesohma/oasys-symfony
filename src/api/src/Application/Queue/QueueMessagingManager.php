<?php

declare(strict_types=1);

namespace App\Application\Queue;

use App\Application\Event\ArchiveEvent;
use App\Application\Event\FinishEvent;
use App\Application\Event\NotifyUpcomingEvent;
use App\Application\Event\StartEvent;
use App\Application\Program\ArchiveProgram;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Repository\EventRepository;
use App\Domain\Util\Time;
use RedisClient\RedisClient;
use function Safe\json_encode;

final class QueueMessagingManager
{
    use Time;

    private EventRepository $eventRepository;
    private RedisClient $redisClient;

    public function __construct(EventRepository $eventRepository, RedisClient $redisClient)
    {
        $this->eventRepository = $eventRepository;
        $this->redisClient = $redisClient;
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function queue(string $eventId): void
    {
        $event = $this->eventRepository->mustFindOneById($eventId);
        $now = $this->getCurrentTime();
        $startTime = $event->getDateEvent();
        $endTime = $event->getDateEventEnd();

        if ($startTime === null || $endTime === null) {
            throw new InvalidDateValue('Cannot queue event without a date');
        }

        $startTime = $this->convertTime($startTime);
        $endTime = $this->convertTime($endTime);

        if ($startTime->modify('-1 day') > $now) {
            $upcomingEventNotificationTime = $startTime->modify('-1 day');
            $upcomingEventNotificationMessage = json_encode([
                'id' => $event->getId(),
                'useCase' => NotifyUpcomingEvent::class,
            ]);
            $this->redisClient->zadd('queue', [
                $upcomingEventNotificationMessage => $upcomingEventNotificationTime->getTimestamp(),
            ]);
        }

        if ($startTime >= $now) {
            $startEventMessage = json_encode([
                'id' => $event->getId(),
                'useCase' => StartEvent::class,
            ]);

            $endEventMessage = json_encode([
                'id' => $event->getId(),
                'useCase' => FinishEvent::class,
            ]);

            $this->redisClient->zadd('queue', [
                $startEventMessage => $startTime->getTimestamp(),
                $endEventMessage => $endTime->getTimestamp(),
            ]);
        }

        $createdAt = $event->getCreatedAt();
        if ($createdAt !== null) {
            $createdAt = $this->convertTime($createdAt);
            $archiveTime = $createdAt->modify('+2 years');
            $archiveEventMessage = json_encode([
                'id' => $event->getId(),
                'useCase' => ArchiveEvent::class,
            ]);

            $this->redisClient->zadd('queue', [
                $archiveEventMessage => $archiveTime->getTimestamp(),
            ]);
        }

        $program = $event->getProgram();
        if ($program === null) {
            return;
        }

        $programDateEnd = $program->getDateEnd();
        if (! $programDateEnd) {
            return;
        }

        $archiveProgramTime = $this->convertTime($programDateEnd)->modify('+2 years');
        $archiveEventMessage = json_encode([
            'id' => $program->getId(),
            'useCase' => ArchiveProgram::class,
        ]);

        $this->redisClient->zadd('queue', [
            $archiveEventMessage => $archiveProgramTime->getTimestamp(),
        ]);

        if ($programDateEnd <= $now->modify('+7 days')) {
            return;
        }

        /*$remindFinishProgramTime = $this->convertTime($programDateEnd)->modify('-7 days');
        $remindFinishProgramMessage = json_encode([
            'id' => $program->getId(),
            'useCase' => RemindFinishProgram::class,
        ]);

        $this->redisClient->zadd('queue', [
            $remindFinishProgramMessage => $remindFinishProgramTime->getTimestamp(),
        ]);*/
    }
}
