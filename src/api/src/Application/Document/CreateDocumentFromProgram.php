<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\DocumentEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\ProgramCoachingIndividualRepository;
use App\Domain\Repository\ProgramRepository;

final class CreateDocumentFromProgram
{
    private CreateDocument $createDocument;
    private ProgramRepository $programRepository;
    private SendDocumentToSignFromProgram $sendDocumentToSignFromProgram;
    private DocumentRepository $documentRepository;
    private ProgramCoachingIndividualRepository $programCoachingIndividualRepository;

    public function __construct(
        CreateDocument $createDocument,
        DocumentRepository $documentRepository,
        SendDocumentToSignFromProgram $sendDocumentToSignFromProgram,
        ProgramRepository $programRepository,
        ProgramCoachingIndividualRepository $programCoachingIndividualRepository
    ) {
        $this->documentRepository = $documentRepository;
        $this->createDocument = $createDocument;
        $this->programRepository = $programRepository;
        $this->sendDocumentToSignFromProgram = $sendDocumentToSignFromProgram;
        $this->programRepository = $programRepository;
        $this->programCoachingIndividualRepository = $programCoachingIndividualRepository;
    }

    /**
     * @throws NotFound
     * @throws YouSignError
     */
    public function create(
        string $name,
        string $description,
        string $tags,
        ?string $fileDescriptorId,
        string $authorId,
        string $categoryId,
        string $programId,
        bool $toSign,
        string $webHookUrlYouSign,
        string $rootPath,
        ?string $elaborationDate = null,
        bool $hidden = false,
        ?string $type = null,
        ?string $articleLink = null,
        bool $toBeDisplayedInHomePage = false,
        ?string $livrableId = null
    ): Document {
        $program = $this->programRepository->mustFindOneById($programId);
        $document = $this->createDocument->create(
            $name,
            $description,
            $tags,
            DocumentEnum::PROTECTED_CODE,
            $fileDescriptorId,
            $authorId,
            $categoryId,
            $elaborationDate,
            $type,
            $articleLink,
            $toBeDisplayedInHomePage,
            $toSign,
            $hidden,
            $livrableId
        );

        $this->sendDocumentToSignFromProgram->sendDocumentToSignFromProgram($program, $document, $webHookUrlYouSign, $rootPath);

        $this->documentRepository->save($document);
        $program->addDocument($document);
        $this->programRepository->save($program);

        return $document;
    }
}
