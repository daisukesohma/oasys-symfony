<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Model\ProfessionalCategory;
use App\Domain\Repository\ProfessionalCategoryRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllProfessionalCategories
{
    private ProfessionalCategoryRepository $professionalCategoryRepositories;

    public function __construct(ProfessionalCategoryRepository $professionalCategoryRepositories)
    {
        $this->professionalCategoryRepositories = $professionalCategoryRepositories;
    }

    /**
     * @return ResultIterator|ProfessionalCategory[]
     */
    public function getAll(): ResultIterator
    {
        return $this->professionalCategoryRepositories->findAll();
    }
}
