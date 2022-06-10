<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\DocumentSigner;
use TheCodingMachine\TDBM\ResultIterator;

interface DocumentSignerRepository
{
    public function save(DocumentSigner $company): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): DocumentSigner;

    /**
     * @throws NotFound
     */
    public function mustFindOneByDocumentMember(string $documentId, string $memberId): DocumentSigner;

    /**
     * @return DocumentSigner[]|ResultIterator
     *
     * @throws NotFound
     */
    public function findByDocumentId(string $documentId): ResultIterator;

    /**
     * @throws NotFound
     */
    public function findByDocumentUser(string $documentId, string $userId): DocumentSigner;
}
