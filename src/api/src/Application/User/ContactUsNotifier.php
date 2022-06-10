<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Model\User;

interface ContactUsNotifier
{
    public function notify(User $user, string $text): void;
}
