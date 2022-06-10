<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Candidate;
use App\Domain\Model\Event;
use App\Domain\Model\Program;
use App\Domain\Model\User;
use Porpaginas\Result;
use TheCodingMachine\TDBM\ResultIterator;

interface UserRepository
{
    public function save(User $user): void;

    public function saveNoLog(User $user): void;

    public function findOneByEmail(string $email): ?User;

    public function checkEmailUnique(string $email, ?string $userId = null): bool;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): User;

    /**
     * @param string[] $types
     *
     * @return User[]|ResultIterator
     */
    public function findByFilters(?string $search, ?string $company, ?array $types, ?string $role, ?string $companyId, ?string $programId, ?string $coachId, ?string $sortColumn, ?string $sortDirection): ResultIterator;

    /**
     * @return User[]|ResultIterator
     */
    public function findUsersToAssociateToProgram(?string $search = null, ?string $companyId = null): ResultIterator;

    public function getLoggedUser(): User;

    /**
     * @param string[]|null $statuses
     *
     * @return Candidate[]|Result
     */
    public function getFilteredCandidates(User $coach, ?string $email, ?string $lastName, ?string $firstName, ?array $statuses = null, ?string $programType = null, ?string $eventType = null, ?string $date = null): Result;

    /**
     * @throws NotFound
     */
    public function mustFindCandidateById(string $id, ?string $programId = null): Candidate;

    public function getNextEvent(string $userId): ?Event;

    /**
     * @return Event[]|Result
     */
    public function getEventsWithoutProgram(string $userId): Result;

    /**
     * @return ResultIterator|User[]
     */
    public function findUsersForExport(): ResultIterator;

    /**
     * @return Result|Event[]
     */
    public function getProgramEventsForCandidate(Program $program, User $candidate, bool $fetchOnlyAttending = false, ?string $dateStart = null, ?string $dateEnd = null): Result;

    /**
     * @return Result|User[]
     */
    public function getUsersForProgram(string $programId): Result;
}
