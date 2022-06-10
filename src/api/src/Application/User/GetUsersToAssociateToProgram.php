<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Repository\UserRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetUsersToAssociateToProgram
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsers(?string $search = null, ?string $companyId = null): ResultIterator
    {
        return $this->userRepository->findUsersToAssociateToProgram($search, $companyId);
    }
}
