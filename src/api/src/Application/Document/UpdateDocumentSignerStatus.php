<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\ProcedureYouSignStatusEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\DocumentSigner;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\DocumentSignerRepository;

final class UpdateDocumentSignerStatus
{
    private DocumentSignerRepository $documentSignerRepository;
    private DocumentRepository $documentRepository;

    public function __construct(
        DocumentSignerRepository $documentSignerRepository,
        DocumentRepository $documentRepository
    ) {
        $this->documentSignerRepository = $documentSignerRepository;
        $this->documentRepository = $documentRepository;
    }

    /**
     * @throws NotFound
     */
    public function updateDocumentSigner(
        string $procedureId,
        string $memberId
    ): DocumentSigner {
        $document = $this->documentRepository->mustFindOneByProcedureId($procedureId);
        $documentSigner = $this->documentSignerRepository->mustFindOneByDocumentMember($document->getId(), $memberId);

        $documentSigner->setStatusSignature(ProcedureYouSignStatusEnum::MEMBER_DONE);
        $this->documentSignerRepository->save($documentSigner);

        return $documentSigner;
    }
}
