<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Repository\DocumentRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllDocumentsForCandidate
{
    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function getAll(
        ?string $search,
        ?string $tagSearch,
        ?string $categoryId = null,
        ?string $programId = null,
        ?string $createdAt = null,
        ?string $sortColumn = 'createdAt',
        ?string $sortDirection = 'desc'
    ): ResultIterator {
        return $this->documentRepository->findByFiltersForCandidate(
            $search,
            $tagSearch,
            $categoryId,
            $programId,
            $createdAt,
            $sortColumn,
            $sortDirection
        );
    }
}
