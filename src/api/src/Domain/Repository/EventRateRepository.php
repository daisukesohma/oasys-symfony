<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Model\EventRate;
use App\Domain\Model\User;

interface EventRateRepository
{
    /**
     * @throws NotFound
     */
    public function findOneByEventAndUser(Event $event, User $user): ?EventRate;

    public function save(EventRate $eventRate): void;
}
