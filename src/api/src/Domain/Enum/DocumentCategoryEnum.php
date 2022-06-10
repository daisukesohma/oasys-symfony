<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class DocumentCategoryEnum implements StringEnum
{
    public const LIVRABLE = 'livrable';
    public const HOMEPAGE = 'home_page';
    public const CUSTOM = 'custom';
    public const TOOLBOX = 'toolbox';

    public const LIVRABLE_LABEL = 'Livrables';
    public const HOMEPAGE_LABEL = 'Page d\'accueil';
    public const CUSTOM_LABEL = 'Personnalisés';
    public const TOOLBOX_LABEL = 'Toolbox';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::LIVRABLE => self::LIVRABLE_LABEL,
            self::HOMEPAGE => self::HOMEPAGE_LABEL,
            self::CUSTOM => self::CUSTOM_LABEL,
            self::TOOLBOX => self::TOOLBOX_LABEL,
        ];
    }
}
