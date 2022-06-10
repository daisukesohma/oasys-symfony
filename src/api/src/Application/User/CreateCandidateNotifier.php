<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Model\User;

interface CreateCandidateNotifier
{
    public function notify(User $user, string $tokenPassword, string $programType, User $coach): void;
}
