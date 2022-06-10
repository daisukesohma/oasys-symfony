<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class EventMeetingEnum implements StringEnum
{
    public const PRESENTIAL = 'presential';
    public const VISIO = 'visio';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::PRESENTIAL,
            self::VISIO,
        ];
    }
}
