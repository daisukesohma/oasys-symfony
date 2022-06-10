<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Model\Document;

interface InviteExternalUserToSignDocument
{
    public function notify(Document $document, string $email, string $firstName, string $lastname, string $memberId): void;
}
