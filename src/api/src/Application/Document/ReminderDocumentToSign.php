<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\ProcedureYouSignStatusEnum;
use App\Domain\Exception\EmailError;
use App\Domain\Exception\NotFound;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\DocumentSignerRepository;

class ReminderDocumentToSign
{
    private DocumentRepository $documentRepository;
    private RemindUserToSignDocumentNotifier $reminderDocumentToSignNotifier;
    private DocumentSignerRepository $documentSignerRepository;

    public function __construct(
        DocumentRepository $documentRepository,
        DocumentSignerRepository $documentSignerRepository,
        RemindUserToSignDocumentNotifier $reminderDocumentToSignNotifier
    ) {
        $this->reminderDocumentToSignNotifier = $reminderDocumentToSignNotifier;
        $this->documentRepository = $documentRepository;
        $this->documentSignerRepository = $documentSignerRepository;
    }

    /**
     * @param mixed[] $members
     *
     * @throws NotFound
     * @throws EmailError
     */
    public function reminderDocumentToSign(array $members, string $procedureId): bool
    {
        $document = $this->documentRepository->mustFindOneByProcedureId($procedureId);

        $programs = $document->getPrograms();
        $coach = null;
        if ($programs && ! empty($programs[0]->getCoaches())) {
            $coach = $programs[0]->getCoaches()[0];
        }

        $documentSigner = null;
        if ($coach) {
            $documentSigner = $this->documentSignerRepository->findByDocumentUser($document->getId(), $coach->getId());
        }

        if ($documentSigner && $documentSigner->getStatusSignature() !== ProcedureYouSignStatusEnum::MEMBER_DONE) {
            throw new EmailError('Reminder was called before coach sign document');
        }

        foreach ($members as $member) {
            if ($member['status'] !== ProcedureYouSignStatusEnum::MEMBER_PENDING) {
                continue;
            }

            $this->reminderDocumentToSignNotifier->notify($member['email'], $document);
        }

        return true;
    }
}
