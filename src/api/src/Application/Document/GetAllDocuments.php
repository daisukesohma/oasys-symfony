<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Repository\DocumentRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllDocuments
{
    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function getAll(
        ?string $search,
        ?string $tagSearch,
        ?string $visibility = null,
        ?string $sortColumn = 'createdAt',
        ?string $sortDirection = 'desc',
        ?string $avoidProgram = null,
        ?string $avoidEvent = null,
        ?string $type = null,
        ?bool $displayedInHomePage = null,
        ?bool $avoidHidden = false,
        ?string $categoryId = null
    ): ResultIterator {
        return $this->documentRepository->findByFilters(
            $search,
            $tagSearch,
            $visibility,
            $sortColumn,
            $sortDirection,
            $avoidProgram,
            $avoidEvent,
            $type,
            $displayedInHomePage,
            $avoidHidden,
            $categoryId
        );
    }
}
