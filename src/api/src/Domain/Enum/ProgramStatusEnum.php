<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class ProgramStatusEnum implements StringEnum
{
    public const UPCOMING = 'upcoming';
    public const INPROGRESS = 'inprogress';
    public const FINISHED = 'finished';
    public const ARCHIVED = 'archived';

    /**
     * @inheritDoc
     */
    public static function values(): array
    {
        return [
            self::UPCOMING,
            self::INPROGRESS,
            self::FINISHED,
            self::ARCHIVED,
        ];
    }
}
