<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Repository\DocumentRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetDocumentsLivrable
{
    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function getAll(string $search, ?string $programId): ResultIterator
    {
        return $this->documentRepository->findForLivrable($search, $programId);
    }
}
