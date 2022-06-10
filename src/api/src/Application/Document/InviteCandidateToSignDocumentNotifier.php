<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Model\Document;
use App\Domain\Model\User;

interface InviteCandidateToSignDocumentNotifier
{
    public function notify(Document $document, User $user, string $memberId): void;
}
