<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class EventYouSignEnum implements StringEnum
{
    public const PROCEDURE_FINISHED = 'procedure.finished';
    public const MEMBER_FINISHED = 'member.finished';
    public const REMINDER_EXECUTED = 'reminder.executed';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::PROCEDURE_FINISHED,
            self::MEMBER_FINISHED,
            self::REMINDER_EXECUTED,
        ];
    }
}
