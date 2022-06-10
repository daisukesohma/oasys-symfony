<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use TheCodingMachine\TDBM\ResultIterator;

interface DocumentRepository
{
    public function save(Document $company): void;

    public function saveNoLog(Document $document): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): Document;

    /**
     * @throws NotFound
     */
    public function mustFindOneByProcedureId(string $id): Document;

    /**
     * @return Document[]|ResultIterator
     */
    public function findByFilters(
        ?string $search,
        ?string $tagSearch,
        ?string $visibility,
        ?string $sortColumn,
        ?string $sortDirection,
        ?string $avoidProgram,
        ?string $avoidEvent,
        ?string $type,
        ?bool $displayedInHomePage,
        ?bool $avoidHidden = false,
        ?string $categoryId = null
    ): ResultIterator;

    public function findByFiltersForCoach(
        ?string $search,
        ?string $tagSearch,
        ?string $visibility,
        ?string $sortColumn,
        ?string $sortDirection,
        ?string $avoidProgram,
        ?string $avoidEvent,
        ?string $type,
        ?bool $displayedInHomePage,
        ?bool $avoidHidden,
        ?string $categoryId,
        ?bool $signaturePending,
        ?bool $signedByCoach,
        ?bool $signedByCandidate,
        ?string $livrableId,
        ?string $programId,
        ?string $eventId,
        ?string $createdAt
    ): ResultIterator;

    /**
     * @return Document[]|ResultIterator
     */
    public function findForLivrable(string $search, ?string $programId): ResultIterator;

    /**
     * @return Document[]|ResultIterator
     */
    public function findByFiltersForAdmin(
        ?string $search,
        ?string $tagSearch,
        ?string $visibility,
        ?string $sortColumn,
        ?string $sortDirection,
        ?string $avoidProgram,
        ?string $avoidEvent,
        ?string $type,
        ?bool $displayedInHomePage,
        ?bool $avoidHidden,
        ?string $categoryId,
        ?bool $signaturePending,
        ?bool $signedByCoach,
        ?bool $signedByCandidate,
        ?string $livrableId,
        ?string $programId,
        ?string $authorId,
        ?string $eventId,
        ?string $createdAt
    ): ResultIterator;

    /**
     * @return mixed[]
     */
    public function findByFiltersForExport(
        ?string $search,
        ?string $tagSearch,
        ?string $visibility,
        ?string $sortColumn,
        ?string $sortDirection,
        ?string $avoidProgram,
        ?string $avoidEvent,
        ?string $type,
        ?bool $displayedInHomePage,
        ?bool $avoidHidden,
        ?string $categoryId,
        ?bool $signaturePending,
        ?bool $signedByCoach,
        ?bool $signedByCandidate,
        ?string $livrableId,
        ?string $programId,
        ?string $authorId,
        ?string $eventId,
        ?string $createdAt
    ): array;

    public function findByFiltersForCandidate(
        ?string $search,
        ?string $tagSearch,
        ?string $categoryId,
        ?string $programId,
        ?string $createdAt,
        ?string $sortColumn,
        ?string $sortDirection
    ): ResultIterator;

    public function delete(Document $document): void;
}
