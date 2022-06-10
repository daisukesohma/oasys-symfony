<?php

declare(strict_types=1);

namespace App\Domain\Util;

use DateTimeImmutable as UnsafeDateTimeImmutable;
use DateTimeZone;
use Safe\DateTimeImmutable;

trait Time
{
    private function getCurrentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));
    }

    private function convertTime(?UnsafeDateTimeImmutable $time): DateTimeImmutable
    {
        if ($time === null) {
            return new DateTimeImmutable();
        }

        return new DateTimeImmutable($time->format('Y-m-d H:i:s'), new DateTimeZone('Europe/Paris'));
    }
}
