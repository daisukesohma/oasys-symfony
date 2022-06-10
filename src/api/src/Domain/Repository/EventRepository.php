<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Model\User;
use Safe\DateTimeImmutable;
use TheCodingMachine\TDBM\ResultIterator;

interface EventRepository
{
    public function save(Event $event): void;

    public function saveNoLog(Event $event): void;

    public function delete(Event $event): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): Event;

    /**
     * @return ResultIterator|Event[]
     */
    public function getEventsWithoutProgramForUser(User $user): ResultIterator;

    /**
     * @return ResultIterator|Event[]
     */
    public function findByFilters(?string $search, ?string $status, ?string $organizer = null, ?string $user = null, ?string $startDate = null, ?string $endDate = null, ?string $sortColumn = 'updatedAt', ?string $sortDirection = 'desc', ?string $type = null): ResultIterator;

    /**
     * @return Event[]
     */
    public function getEventsToQueue(): array;

    /**
     * @return Event[]
     */
    public function getEventsToArchive(): array;

    /**
     * @return Event[]
     */
    public function getEventsToFinish(): array;

    public function getEventBetween(DateTimeImmutable $startTime, DateTimeImmutable $endTime, User $user): ?Event;

    /**
     * @return Event[]|ResultIterator
     */
    public function findAll(): ResultIterator;

    /**
     * @return Event[]|ResultIterator
     */
    public function findByType(string $type): ResultIterator;
}
