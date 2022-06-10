<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface SignRepository
{
    public function downloadSignedFile(string $fileid, ?string $mode = null): string;

    /**
     * @param array|mixed[] $parameters
     */
    public function advancedProcedureCreate(array $parameters, bool $webhook = false, string $webhookMethod = '', string $webhookUrl = '', string $webhookHeader = ''): string;

    public function advancedProcedureAddFile(string $filepathOrFileContent, string $namefile, bool $filecontent = false): string;

    public function advancedProcedureAddMember(string $firstname, string $lastname, string $email, string $phone): string;

    public function advancedProcedurePut(): string;

    public function advancedProcedureFileObject(string $position, string $page, string $mention, string $mention2, string $reason): string;

    public function getIdAdvProc(): string;
}
