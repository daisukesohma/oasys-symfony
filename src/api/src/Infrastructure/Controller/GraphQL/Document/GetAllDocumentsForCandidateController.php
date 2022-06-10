<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\GetAllDocumentsForCandidate;
use App\Domain\Model\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllDocumentsForCandidateController extends AbstractController
{
    private GetAllDocumentsForCandidate $getAllDocuments;

    public function __construct(GetAllDocumentsForCandidate $getAllDocuments)
    {
        $this->getAllDocuments = $getAllDocuments;
    }

    /**
     * @return ResultIterator|Document[]
     *
     * @Query
     * @Logged
     */
    public function getAllDocumentsForCandidate(
        ?string $search,
        ?string $tagSearch,
        ?string $categoryId = null,
        ?string $programId = null,
        ?string $createdAt = null,
        ?string $sortColumn = 'createdAt',
        ?string $sortDirection = 'desc'
    ): ResultIterator {
        /** @var ResultIterator|Document[] $result */
        $result = $this->getAllDocuments->getAll($search, $tagSearch, $categoryId, $programId, $createdAt, $sortColumn, $sortDirection);

        return $result;
    }
}
