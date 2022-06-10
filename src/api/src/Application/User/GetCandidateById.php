<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Candidate;
use App\Domain\Repository\UserRepository;
use function Safe\ini_set;

final class GetCandidateById
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws NotFound
     */
    public function get(string $id, ?string $programId = null): Candidate
    {
        ini_set('memory_limit', '-1');

        return $this->userRepository->mustFindCandidateById($id, $programId);
    }
}
