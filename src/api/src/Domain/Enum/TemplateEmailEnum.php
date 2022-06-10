<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class TemplateEmailEnum implements StringEnum
{
    public const SITE_OASYS_LINK = 'app.site_oasys_link';
    public const LINKEDIN_LINK = 'app.linkedin_link';
    public const TWITTER_LINK = 'app.twitter_link';

    /**
     * @inheritDoc
     */
    public static function values(): array
    {
        return [
            self::SITE_OASYS_LINK,
            self::LINKEDIN_LINK,
            self::TWITTER_LINK,
        ];
    }
}
