<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Model\User;

interface EventPresentialNotifier
{
    /**
     * @throws InvalidDateValue
     * @throws NotFound
     */
    public function notify(Event $event, User $user): void;
}
