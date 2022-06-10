<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Model\Document;

interface RemindUserToSignDocumentNotifier
{
    public function notify(string $mail, Document $document): void;
}
