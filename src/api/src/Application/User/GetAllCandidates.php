<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Model\Candidate;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\UserRepository;
use Porpaginas\Result;

final class GetAllCandidates
{
    private UserRepository $userRepository;
    private ProgramRepository $programRepository;

    public function __construct(UserRepository $userRepository, ProgramRepository $programRepository)
    {
        $this->userRepository = $userRepository;
        $this->programRepository = $programRepository;
    }

    /**
     * @param string[]|null $statuses
     *
     * @return Candidate[]|Result
     */
    public function getAll(?string $email = null, ?string $lastName = null, ?string $firstName = null, ?array $statuses = null, ?string $programType = null, ?string $eventType = null, ?string $date = null): Result
    {
        $coach = $this->userRepository->getLoggedUser();

        return $this->userRepository->getFilteredCandidates($coach, $email, $lastName, $firstName, $statuses, $programType, $eventType, $date);
    }
}
