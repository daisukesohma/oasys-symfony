<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class ProfessionalCategoryEnum implements StringEnum
{
    public const EMPLOYEE = 'employee';
    public const QUALIFIED_EMPLOYEE = 'qualified_employee';
    public const TECHNICIAN = 'technician';
    public const SUPERVISOR = 'supervisor';
    public const FRAME = 'frame';
    public const EXECUTIVE = 'executive';

    public const EMPLOYEE_LABEL = 'Employé.e / Ouvrier.ère';
    public const QUALIFIED_EMPLOYEE_LABEL = 'Employé.e qualifié.e';
    public const TECHNICIAN_LABEL = 'Technicien.nne';
    public const SUPERVISOR_LABEL = 'Agent de maîtrise';
    public const FRAME_LABEL = 'Cadre';
    public const EXECUTIVE_LABEL = 'Cadre dirigeant.e';

    /**
     * @inheritDoc
     */
    public static function values(): array
    {
        return [
            self::EMPLOYEE => self::EMPLOYEE_LABEL,
            self::QUALIFIED_EMPLOYEE => self::QUALIFIED_EMPLOYEE_LABEL,
            self::TECHNICIAN => self::TECHNICIAN_LABEL,
            self::SUPERVISOR => self::SUPERVISOR_LABEL,
            self::FRAME => self::FRAME_LABEL,
            self::EXECUTIVE => self::EXECUTIVE_LABEL,
        ];
    }
}
