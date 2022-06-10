<?php

declare(strict_types=1);

namespace App\Infrastructure\YouSign;

use App\Domain\Enum\ProcedureYouSignStatusEnum;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Document;
use App\Domain\Model\DocumentSigner;
use function array_push;
use function explode;
use function is_string;
use function Safe\json_decode;

trait YousignHelper
{
    public function createProcedureWithWebHook(string $name, string $filepath, string $description): string
    {
        $post = [
            'name' => $name,
            'description' => $description,
            'start' => false,
        ];

        $result = $this->youSignClient->AdvancedProcedureCreate(
            $post,
            true,
            'GET',
            'https://webhook.site/26a75c2d-89ac-4906-b706-8f7ce1f3dc8e'
        );

        $result = $this->youSignClient->AdvancedProcedureAddFile($filepath, $name);

        $idProcedure = $this->youSignClient->getIdAdvProc();
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
    public function addMemberToProcedureAndStart(array $members, Document $document): array
    {
        $newDocumentSigners = [];
        foreach ($members as $member) {
            $newMember = json_decode($this->youSignClient->AdvancedProcedureAddMember($member['firstname'], $member['lastname'], $member['email'], $member['phone']));
            $result = $this->youSignClient->AdvancedProcedureFileObject($member['position'], $member['page'], $member['mention'], $member['mention2'], $member['reason']);

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

        $result = $this->youSignClient->AdvancedProcedurePut();

        if (is_string($result)) {
            $result = json_decode($result, true);
        }

        foreach ($newDocumentSigners as $newDocumentSigner) {
            $this->documentSignerRepository->save($newDocumentSigner);
        }

        return $result;
    }
}
