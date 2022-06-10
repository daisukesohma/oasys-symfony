<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Model\User;
use Safe\DateTimeImmutable;
use function explode;
use function str_replace;
use function strtolower;

/**
 * Use case for importing users from a Program e-PIC candidates
 *
 * Column layout:
 * 1. First Name
 * 2. Last Name
 * 3. Email
 * 4. Phone
 * 5. Type
 * 6. Role(s) (comma separated list of role name)
 * 7. Company Name
 * 8. Status
 * 9. Address
 * 10. LinkedIn
 * 11. Function
 * 12. Previous Function
 * 13. Seniority Date
 * 14. N+! First Name (for candidate only)
 * 15. N+1 Last Name (for candidate only)
 * 16. N+! Email (for candidate only)
 * 17. N+1 Phone (for candidate only)
 */
final class ImportUsersForProgramPic
{
    use ImportUsersFromExcel;

    private CreateUserFromImport $importUser;
    private Program $program;

    public function __construct(CreateUserFromImport $importUser)
    {
        $this->importUser = $importUser;
    }

    public function setProgram(Program $program): void
    {
        $this->program = $program;
    }

    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    protected function mapExcelRowToArray(array $row): array
    {
        return [
            'civility' => str_replace('.', '', strtolower((string) $row[0])),
            'firstName' => (string) $row[1],
            'lastName' => (string) $row[2],
            'email' => (string) $row[3],
            'phone' => (string) $row[4],
            'type' => UserTypeEnum::CANDIDATE,
            'roles' => ! empty($row[5]) ? explode(',', $row[5]) : [],
            'company' => $row[6],
            'status' => (string) $row[7],
            'address' => (string) $row[8],
            'linkedIn' => (string) $row[9],
            'function' => (string) $row[10],
            'previousFunction' => (string) $row[11],
            'seniorityDate' => empty($row[12]) ? null : DateTimeImmutable::createFromFormat('d/m/Y', $row[12])->format('c'),
            'programType' => (string) $row[13],
            'nFirstName' => (string) $row[14],
            'nLastName' => (string) $row[15],
            'nEmail' => (string) $row[16],
            'nPhone' => (string) $row[17],
            'service' => (string) $row[18],
            'ville' => (string) $row[19],
            'department' => (string) $row[20],
            'postCode' => (string) $row[21],
            'birthDate' => empty($row[22]) ? null : DateTimeImmutable::createFromFormat('d/m/Y', $row[22])->format('c'),
            'professionalCategory' => (string) $row[23],
            'annualCompensation' => (string) $row[24],
            'userCodePostal' => (string) $row[25],
            'userDepartment' => (string) $row[26],
            'userCity' => (string) $row[27],
        ];
    }

    /**
     * @param mixed[] $row
     *
     * @throws InvalidStringValue
     * @throws NotFound
     */
    protected function importUser(array $row, bool $create = true): ?User
    {
        return $this->importUser->import($row, $create, false, true, $this->program->getCompany());
    }
}
