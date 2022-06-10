<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class QuestionThemeEnum implements StringEnum
{
    public const PROCEDURE_RCC = 'PROCEDURE_RCC';
    public const INDEMNITES_DE_RUPTURE = 'INDEMNITES_DE_RUPTURE';
    public const SITUATIONS_PARTICULIERES = 'SITUATIONS_PARTICULIERES';
    public const CONGE_DE_MOBILITE = 'CONGE_DE_MOBILITE';
    public const MESURES_ACCOMPAGNEMENT = 'MESURES_ACCOMPAGNEMENT';
    public const PROJET_EMPLOI_SALARIE = 'PROJET_EMPLOI_SALARIE';
    public const PROJET_CREATION_REPRISE_ENTREPRISE = 'PROJET_CREATION_REPRISE_ENTREPRISE';
    public const PROJET_RECONVERSION_FORMATION_LONGUE = 'PROJET_RECONVERSION_FORMATION_LONGUE';
    public const PROJET_DEPART_RETRAITE = 'PROJET_DEPART_RETRAITE';
    public const PROFESSIONAL_SECURITY_CONTRACT = 'PROFESSIONAL_SECURITY_CONTRACT';
    public const PROCEDURE_PSE = 'PROCEDURE_PSE';

    /**
     * @inheritDoc
     */
    public static function values(): array
    {
        return [
            self::PROCEDURE_RCC,
            self::INDEMNITES_DE_RUPTURE,
            self::SITUATIONS_PARTICULIERES,
            self::CONGE_DE_MOBILITE,
            self::MESURES_ACCOMPAGNEMENT,
            self::PROJET_EMPLOI_SALARIE,
            self::PROJET_CREATION_REPRISE_ENTREPRISE,
            self::PROJET_RECONVERSION_FORMATION_LONGUE,
            self::PROJET_DEPART_RETRAITE,
            self::PROCEDURE_PSE,
            self::PROFESSIONAL_SECURITY_CONTRACT,
        ];
    }
}
