<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\ProcedureYouSignStatusEnum;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Document;
use App\Domain\Model\Program;
use App\Domain\Model\User;
use App\Domain\Repository\ProgramCoachingIndividualRepository;
use function count;

class SendDocumentToSignFromProgram
{
    private SendDocumentToSign $sendDocumentToSign;
    private ProgramCoachingIndividualRepository $programCoachingIndividualRepository;

    public function __construct(
        SendDocumentToSign $sendDocumentToSign,
        ProgramCoachingIndividualRepository $programCoachingIndividualRepository
    ) {
        $this->sendDocumentToSign = $sendDocumentToSign;
        $this->programCoachingIndividualRepository = $programCoachingIndividualRepository;
    }

    /**
     * @throws YouSignError
     * @throws NotFound
     */
    public function sendDocumentToSignFromProgram(Program $program, Document $document, string $webHookYouSignUrl, string $rootPath): void
    {
        if ($program->getType() !== ProgramTypeEnum::INDIVIDUAL || ! $document->getToBeSigned()) {
            return;
        }

        $programSuperior = $this->programCoachingIndividualRepository->mustFindOneById($program->getId());
        $coach = ! empty($program->getCoaches()) ? $program->getCoaches()[0] : null;

        if (! count($program->getUsersByProgramsUsers()) || $coach === null) {
            throw new NotFound(User::class, ['program' => $program->getId()]);
        }
        $user = $program->getUsersByProgramsUsers()[0];

        $members = [
            [
                'user' => $user,
                'firstname' => $user->getFirstName(),
                'lastname' => $user->getLastName(),
                'email' => $user->getEmail(),
                'phone' => $user->getPhone(),
                'page' => 2,
                'position' => '14,53,112,93',
                'mention' => '',
                'mention2' => '',
                'reason' => '',
            ],
            [
                'firstname' => $programSuperior->getFirstName(),
                'lastname' => $programSuperior->getLastName(),
                'email' => $programSuperior->getEmail(),
                'phone' => $programSuperior->getPhone(),
                'page' => 2,
                'position' => '245,54,344,93',
                'mention' => '',
                'mention2' => '',
                'reason' => '',
            ],
            [
                'user' => $coach,
                'validator' => true,
                'firstname' => $coach->getFirstName(),
                'lastname' => $coach->getLastName(),
                'email' => $coach->getEmail(),
                'phone' => $coach->getPhone(),
                'page' => 2,
                'position' => '467,50,566,89',
                'mention' => '',
                'mention2' => '',
                'reason' => '',
            ],
        ];

        $result = $this->sendDocumentToSign->sendDocumentToSign($document, $members, $webHookYouSignUrl, $rootPath);

        $document->setStatusSignature(ProcedureYouSignStatusEnum::PROCEDURE_PENDING);
        $document->setToBeSigned(true);

        if (! $result) {
            throw new YouSignError('An error happen with YouSign API');
        }
    }
}
