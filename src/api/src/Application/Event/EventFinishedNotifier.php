<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Model\Event;
use App\Domain\Model\User;

interface EventFinishedNotifier
{
    public function notify(Event $event, User $user): void;
}
