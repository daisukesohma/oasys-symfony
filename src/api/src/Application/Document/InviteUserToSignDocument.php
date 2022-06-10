<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\ProcedureYouSignStatusEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\EmailError;
use App\Domain\Exception\NotFound;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\DocumentSignerRepository;
use App\Domain\Repository\ProgramCoachingIndividualRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class InviteUserToSignDocument
{
    private DocumentSignerRepository $documentSignerRepository;
    private DocumentRepository $documentRepository;
    private ProgramCoachingIndividualRepository $programCoachingIndividualRepository;
    private InviteCandidateToSignDocumentNotifier $startSignDocumentNotifier;
    private InviteExternalUserToSignDocument $startSignDocumentExternalUserNotifier;

    public function __construct(
        DocumentSignerRepository $documentSignerRepository,
        DocumentRepository $documentRepository,
        InviteCandidateToSignDocumentNotifier $startSignDocumentNotifier,
        InviteExternalUserToSignDocument $startSignDocumentExternalUserNotifier,
        ProgramCoachingIndividualRepository $programCoachingIndividualRepository
    ) {
        $this->documentSignerRepository = $documentSignerRepository;
        $this->documentRepository = $documentRepository;
        $this->programCoachingIndividualRepository = $programCoachingIndividualRepository;
        $this->startSignDocumentNotifier = $startSignDocumentNotifier;
        $this->startSignDocumentExternalUserNotifier = $startSignDocumentExternalUserNotifier;
    }

    /**
     * @throws NotFound
     * @throws EmailError
     */
    public function startSignDocument(
        string $id
    ): ResultIterator {
        $document = $this->documentRepository->mustFindOneById($id);
        $documentsSigners = $this->documentSignerRepository->findByDocumentId($id);

        foreach ($documentsSigners as $documentsSigner) {
            if ($documentsSigner->getStatusSignature() !== ProcedureYouSignStatusEnum::MEMBER_HIDE) {
                continue;
            }

            $documentsSigner->setStatusSignature(ProcedureYouSignStatusEnum::MEMBER_PENDING);
            $this->documentSignerRepository->save($documentsSigner);

            $user = $documentsSigner->getUser();
            $programs = $document->getPrograms();
            $programCoachingIndividual = null;
            if ($programs) {
                $programId = $programs[0]->getId();
                $programCoachingIndividual = $this->programCoachingIndividualRepository->findOneById($programId);
            }

            $memberId =  $documentsSigner->getMemberId();
            if ($memberId && $user && $user->getType()->getId() === UserTypeEnum::CANDIDATE) {
                $this->startSignDocumentNotifier->notify($document, $user, $memberId);
            } elseif ($programCoachingIndividual && $memberId) {
                $this->startSignDocumentExternalUserNotifier->notify(
                    $document,
                    $programCoachingIndividual->getEmail(),
                    $programCoachingIndividual->getFirstName(),
                    $programCoachingIndividual->getLastName(),
                    $memberId
                );
            } else {
                throw new EmailError('Could not send email to start sign document');
            }
        }

        $document->setStatusSignature(ProcedureYouSignStatusEnum::PROCEDURE_ACTIVE);
        $this->documentRepository->save($document);

        return $documentsSigners;
    }
}
