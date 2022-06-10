<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Model\Program;
use App\Domain\Model\User;

interface ReminderFinishProgramNotifier
{
    public function notify(Program $program, User $user): void;
}
