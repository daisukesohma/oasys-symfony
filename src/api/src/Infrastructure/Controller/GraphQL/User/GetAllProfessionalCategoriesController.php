<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\GetAllProfessionalCategories;
use App\Domain\Model\ProfessionalCategory;
use Porpaginas\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetAllProfessionalCategoriesController extends AbstractController
{
    private GetAllProfessionalCategories $getAllProfessionalCategories;

    public function __construct(GetAllProfessionalCategories $getAllProfessionalCategories)
    {
        $this->getAllProfessionalCategories = $getAllProfessionalCategories;
    }

    /**
     * @return Result|ProfessionalCategory[]
     *
     * @Query
     */
    public function getAllProfessionalCategories(): Result
    {
        /** @var Result|ProfessionalCategory[] $result */
        $result = $this->getAllProfessionalCategories->getAll();

        return $result;
    }
}
