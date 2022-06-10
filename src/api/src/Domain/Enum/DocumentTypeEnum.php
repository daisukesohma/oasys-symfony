<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class DocumentTypeEnum implements StringEnum
{
    public const FILE = 'file';
    public const ARTICLE = 'article';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::FILE,
            self::ARTICLE,
        ];
    }
}
