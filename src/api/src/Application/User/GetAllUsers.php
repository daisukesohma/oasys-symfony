<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllUsers
{
    private UserRepository $userRepository;
    private CompanyRepository $companyRepository;

    public function __construct(UserRepository $userRepository, CompanyRepository $companyRepository)
    {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param string[] $types
     */
    public function getAll(?string $search, ?string $companyName = null, ?array $types = [], ?string $roleId = null, ?string $companyId = null, ?string $programId = null, ?string $coachId = null, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): ResultIterator
    {
        return $this->userRepository->findByFilters($search, $companyName, $types, $roleId, $companyId, $programId, $coachId, $sortColumn, $sortDirection);
    }
}
