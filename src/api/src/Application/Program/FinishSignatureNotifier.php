<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Model\Document;

interface FinishSignatureNotifier
{
    public function notify(string $mail, Document $document): void;
}
