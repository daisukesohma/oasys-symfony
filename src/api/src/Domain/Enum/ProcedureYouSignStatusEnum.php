<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class ProcedureYouSignStatusEnum implements StringEnum
{
    public const PROCEDURE_ACTIVE = 'active';
    public const PROCEDURE_PENDING = 'pending';
    public const PROCEDURE_FINISHED = 'finished';

    public const MEMBER_PENDING = 'pending';
    public const MEMBER_DONE = 'done';
    public const MEMBER_HIDE = 'hide';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::PROCEDURE_ACTIVE,
            self::PROCEDURE_FINISHED,
        ];
    }
}
