<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class ProgramTypeEnum implements StringEnum
{
    public const INDIVIDUAL = 'individual';
    public const GROUP = 'group';
    public const PIC = 'pic';
    public const OUTPLACEMENT = 'outplacement';

    /**
     * @inheritDoc
     */
    public static function values(): array
    {
        return [
            self::INDIVIDUAL,
            self::GROUP,
            self::PIC,
            self::OUTPLACEMENT,
        ];
    }
}
