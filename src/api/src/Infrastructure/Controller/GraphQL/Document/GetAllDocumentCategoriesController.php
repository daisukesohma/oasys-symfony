<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\GetAllDocumentCategories;
use App\Domain\Model\DocumentCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllDocumentCategoriesController extends AbstractController
{
    private GetAllDocumentCategories $getAllDocumentCategories;

    public function __construct(GetAllDocumentCategories $getAllDocumentCategories)
    {
        $this->getAllDocumentCategories = $getAllDocumentCategories;
    }

    /**
     * @return ResultIterator|DocumentCategory[]
     *
     * @Query
     * @Logged
     */
    public function getAllDocumentCategories(): ResultIterator
    {
        /** @var ResultIterator|DocumentCategory[] $result */
        $result = $this->getAllDocumentCategories->getAll();

        return $result;
    }
}
