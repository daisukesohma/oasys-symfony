<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Application\Document\AddDocumentNotifier;
use App\Application\Document\DuplicateDocument;
use App\Application\Document\SendDocumentToSignFromProgram;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Program;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\ProgramRepository;

final class AddDocumentToProgram
{
    private DuplicateDocument $duplicateDocument;
    private ProgramRepository $programRepository;
    private AddDocumentNotifier $addDocumentNotifier;
    private SendDocumentToSignFromProgram $sendDocumentToSignFromProgram;
    private DocumentRepository $documentRepository;

    public function __construct(
        DuplicateDocument $duplicateDocument,
        ProgramRepository $programRepository,
        AddDocumentNotifier $addDocumentNotifier,
        SendDocumentToSignFromProgram $sendDocumentToSignFromProgram,
        DocumentRepository $documentRepository
    ) {
        $this->duplicateDocument = $duplicateDocument;
        $this->programRepository = $programRepository;
        $this->addDocumentNotifier = $addDocumentNotifier;
        $this->documentRepository = $documentRepository;
        $this->sendDocumentToSignFromProgram = $sendDocumentToSignFromProgram;
    }

    /**
     * @throws NotFound
     * @throws YouSignError
     */
    public function add(string $programId, string $documentId, string $rootPath, string $webHookUrlYouSign): Program
    {
        $document = $this->duplicateDocument->duplicate($documentId, $rootPath);
        $program = $this->programRepository->mustFindOneById($programId);

        $this->sendDocumentToSignFromProgram->sendDocumentToSignFromProgram($program, $document, $webHookUrlYouSign, $rootPath);

        $document->setHidden(true);

        $this->documentRepository->save($document);
        $program->addDocument($document);
        $this->programRepository->save($program);

        if ($document->getToBeSigned()) {
            return $program;
        }

        foreach ($program->getUsersByProgramsUsers() as $user) {
            $this->addDocumentNotifier->notify($document, $user);
        }

        return $program;
    }
}
