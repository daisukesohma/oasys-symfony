<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class RoleEnum implements StringEnum
{
    public const ADMINISTRATEUR_ID = '1';
    public const ADMINISTRATEUR_NAME = 'Administrateur';
    public const CANDIDATE_RDV = 'Candidat reprise RDV';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::ADMINISTRATEUR_NAME,
        ];
    }
}
