<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class EventStatusEnum implements StringEnum
{
    public const CREATED = 'created';
    public const UPCOMING = 'upcoming';
    public const ONGOING = 'ongoing';
    public const FINISHED = 'finished';
    public const ARCHIVED = 'archived';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::CREATED,
            self::UPCOMING,
            self::ONGOING,
            self::FINISHED,
            self::ARCHIVED,
        ];
    }
}
