<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class CoachSpecialityEnum implements StringEnum
{
    public const EMPLOYMENT_TRAINING = 'employment_training';
    public const BUSINESS_CREATION = 'business_creation';
    public const AGE_MEASUREMENTS = 'age_measurements';
    public const GENERALIST = 'generalist';

    public const EMPLOYMENT_TRAINING_LABEL = 'Emploi / Formation / Reconversion';
    public const BUSINESS_CREATION_LABEL = 'Création / Reprise d\'entreprise ou d\'activité';
    public const AGE_MEASUREMENTS_LABEL = 'Fin de carrière / Retraite';
    public const GENERALIST_LABEL = 'Généraliste';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::EMPLOYMENT_TRAINING => self::EMPLOYMENT_TRAINING_LABEL,
            self::BUSINESS_CREATION => self::BUSINESS_CREATION_LABEL,
            self::AGE_MEASUREMENTS => self::AGE_MEASUREMENTS_LABEL,
            self::GENERALIST => self::GENERALIST_LABEL,
        ];
    }
}
