<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\ProcedureYouSignStatusEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Document;
use App\Domain\Model\DocumentSigner;
use App\Domain\Model\FileDescriptor;
use App\Domain\Repository\DocumentSignerRepository;
use App\Domain\Repository\SignRepository;
use function array_push;
use function explode;
use function file_exists;
use function is_string;
use function Safe\json_decode;
use function Safe\parse_url;
use function trim;

class SendDocumentToSign
{
    private SignRepository $signRepository;
    private DocumentSignerRepository $documentSignerRepository;

    public function __construct(
        SignRepository $signRepository,
        DocumentSignerRepository $documentSignerRepository
    ) {
        $this->signRepository = $signRepository;
        $this->documentSignerRepository = $documentSignerRepository;
    }

    /**
     * @param mixed[] $members
     *
     * @throws NotFound
     */
    public function sendDocumentToSign(Document $document, array $members, string $webHookYouSignUrl, string $rootPath): bool
    {
        if ($document->getFileDescriptor() === null) {
            throw new NotFound(FileDescriptor::class, ['Document id' => $document->getId()]);
        }

        $upstream = parse_url($document->getFileDescriptor()->getUpstream());
        $filepath = $rootPath . $upstream['path'];
        if (! file_exists($filepath)) {
            throw new NotFound(FileDescriptor::class, ['filename' => $document->getFileDescriptor()->getName()]);
        }

        $idProcedure = $this->createProcedureWithWebHook($document->getName(), $filepath, "Procédure pour document d'événement", $webHookYouSignUrl);
        $document->setProcedureId($idProcedure);

        $this->addMemberToProcedureAndStart($members, $document);

        return true;
    }

    private function createProcedureWithWebHook(string $name, string $filepath, string $description, string $webHookYouSignUrl): string
    {
        $post = [
            'name' => $name,
            'description' => $description,
            'start' => false,
        ];

        $result = $this->signRepository->AdvancedProcedureCreate(
            $post,
            true,
            'GET',
            $webHookYouSignUrl
        );

        $result = $this->signRepository->AdvancedProcedureAddFile($filepath, $name);

        $idProcedure = $this->signRepository->getIdAdvProc();
        if (empty(explode('/', $idProcedure)[2])) {
            new YouSignError("Procedure can't be created");
        }

        return explode('/', $idProcedure)[2];
    }

    /**
     * @param mixed[] $members
     *
     * @return mixed[]
     */
    private function addMemberToProcedureAndStart(array $members, Document $document): array
    {
        $newDocumentSigners = [];
        foreach ($members as $member) {
            $newMember = json_decode($this->signRepository->AdvancedProcedureAddMember(trim($member['firstname']), trim($member['lastname']), $member['email'], $member['phone']));
            $result = $this->signRepository->AdvancedProcedureFileObject($member['position'], $member['page'], $member['mention'], $member['mention2'], $member['reason']);
            $documentSigner = new DocumentSigner();
            $documentSigner->setDocument($document);
            if (empty(explode('/', $newMember->id)[2])) {
                new YouSignError("Procedure can't be created");
            }
            $documentSigner->setMemberId(explode('/', $newMember->id)[2]);
            if (isset($member['validator']) && $member['validator'] === true) {
                $documentSigner->setStatusSignature(ProcedureYouSignStatusEnum::MEMBER_PENDING);
            } else {
                $documentSigner->setStatusSignature(ProcedureYouSignStatusEnum::MEMBER_HIDE);
            }
            if (isset($member['user'])) {
                $documentSigner->setUser($member['user']);
            }

            array_push($newDocumentSigners, $documentSigner);
        }
        $result = $this->signRepository->AdvancedProcedurePut();

        if (is_string($result)) {
            $result = json_decode($result, true);
        }

        foreach ($newDocumentSigners as $newDocumentSigner) {
            $this->documentSignerRepository->save($newDocumentSigner);
        }

        return $result;
    }
}
