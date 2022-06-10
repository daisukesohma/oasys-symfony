<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Application\Program\FinishSignatureNotifier;
use App\Domain\Enum\ProcedureYouSignStatusEnum;
use App\Domain\Exception\EmailError;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentRepository;

final class UpdateSignDocument
{
    private DocumentRepository $documentRepository;
    private FinishSignatureNotifier $finishSignatureNotifier;

    public function __construct(DocumentRepository $documentRepository, FinishSignatureNotifier $finishSignatureNotifier)
    {
        $this->documentRepository = $documentRepository;
        $this->finishSignatureNotifier = $finishSignatureNotifier;
    }

    /**
     * @throws NotFound
     */
    public function updateSignDocument(
        string $id
    ): Document {
        $document = $this->documentRepository->mustFindOneByProcedureId($id);

        $document->setStatusSignature(ProcedureYouSignStatusEnum::PROCEDURE_FINISHED);

        $this->documentRepository->saveNoLog($document);

        $event = $document->getEvents() ? $document->getEvents()[0] : null;
        $coach = $document->getPrograms() && ! empty($document->getPrograms()[0]->getCoaches()) ? $document->getPrograms()[0]->getCoaches()[0] : null;
        if ($coach) {
            $this->finishSignatureNotifier->notify($coach->getEmail(), $document);
        } elseif ($event) {
            $program = $event->getProgram();

            if ($program) {
                $coach = ! empty($program->getCoaches()) ? $program->getCoaches()[0] : null;
                if ($coach) {
                    $this->finishSignatureNotifier->notify($coach->getEmail(), $document);
                } else {
                    new EmailError('There is no coach link to the program "' . $program->getName() . '"');
                }
            } else {
                new EmailError('There is no program link to this event "' . $event->getName() . '"');
            }
        } else {
            new EmailError('There is no program link to the document "' . $document->getName() . '"');
        }

        return $document;
    }
}
