<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class HostEnum implements StringEnum
{
    public const HOST_URL = 'HOST_URL';
    public const HOST_PROTOCOL = 'HOST_PROTOCOL';
    public const YOUSIGN_TOKEN = 'YOUSIGN_TOKEN';
    public const YOUSIGN_APP = 'YOUSIGN_APP';

    /**
     * @inheritDoc
     */
    public static function values(): array
    {
        return [
            self::HOST_URL,
            self::HOST_PROTOCOL,
            self::YOUSIGN_TOKEN,
        ];
    }
}
