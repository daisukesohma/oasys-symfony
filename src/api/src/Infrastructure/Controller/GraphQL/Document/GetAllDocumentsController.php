<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\GetAllDocuments;
use App\Domain\Model\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllDocumentsController extends AbstractController
{
    private GetAllDocuments $getAllDocuments;

    public function __construct(GetAllDocuments $getAllDocuments)
    {
        $this->getAllDocuments = $getAllDocuments;
    }

    /**
     * @return ResultIterator|Document[]
     *
     * @Query
     * @Logged
     */
    public function getAllDocuments(
        ?string $search = null,
        ?string $tagSearch = null,
        string $visibility = '',
        ?string $sortColumn = 'createdAt',
        ?string $sortDirection = 'desc',
        ?string $avoidProgram = null,
        ?string $avoidEvent = null,
        ?string $type = null,
        ?bool $displayedInHomePage = null,
        ?bool $avoidHidden = false,
        ?string $categoryId = null
    ): ResultIterator {
        /** @var ResultIterator|Document[] $result */
        $result = $this->getAllDocuments->getAll($search, $tagSearch, $visibility, $sortColumn, $sortDirection, $avoidProgram, $avoidEvent, $type, $displayedInHomePage, $avoidHidden, $categoryId);

        return $result;
    }
}
