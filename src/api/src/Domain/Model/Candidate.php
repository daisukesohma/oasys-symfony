<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Enum\EventStatusEnum;
use App\Domain\Repository\UserRepository;
use Porpaginas\Result;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use function array_filter;
use function count;

/**
 * @Type
 */
class Candidate
{
    private User $user;
    private Program $program;
    private UserRepository $userRepository;

    public function __construct(User $user, Program $program, UserRepository $userRepository)
    {
        $this->user = $user;
        $this->program = $program;
        $this->userRepository = $userRepository;
    }

    /**
     * @Field
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @Field
     */
    public function getProgram(): Program
    {
        return $this->program;
    }

    /**
     * @Field
     */
    public function getEventsCount(): int
    {
        return count($this->user->getEventsByEventsUsers());
    }

    /**
     * @Field
     */
    public function getNextEvent(): ?Event
    {
        return $this->userRepository->getNextEvent($this->user->getId());
    }

    /**
     * @Field
     */
    public function getCompletedEventsCount(): int
    {
        return count(array_filter(
            $this->user->getEventsByEventsUsers(),
            static fn(Event $event) => $event->getStatus() === EventStatusEnum::FINISHED || $event->getStatus() === EventStatusEnum::ARCHIVED
        ));
    }

    /**
     * @return Event[]|Result
     *
     * @Field
     */
    public function getEventsWithoutProgram(): Result
    {
        return $this->userRepository->getEventsWithoutProgram($this->user->getId());
    }
}
