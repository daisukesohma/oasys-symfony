<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use Safe\DateTimeImmutable;
use function explode;
use function Safe\ini_set;
use function str_replace;
use function strtolower;

/**
 * Use case for importing users from an xlsx file
 *
 * Column layout:
 * 1. First Name
 * 2. Last Name
 * 3. Email
 * 4. Phone
 * 5. Type
 * 6. Role(s) (comma separated list of role name)
 * 7. Company Name
 * 8. Coach Email
 * 9. Status
 * 10. Address
 * 11. LinkedIn
 * 12. Function
 * 13. Previous Function
 * 14. Seniority Date
 * 15. N+! First Name (for candidate only)
 * 16. N+1 Last Name (for candidate only)
 * 17. N+! Email (for candidate only)
 * 18. N+1 Phone (for candidate only)
 */
final class ImportUsers
{
    use ImportUsersFromExcel;

    private CreateUserFromImport $importUser;

    public function __construct(CreateUserFromImport $importUser)
    {
        $this->importUser = $importUser;
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
            'type' => (string) $row[5],
            'roles' => ! empty($row[6]) ? explode(',', $row[6]) : [],
            'company' => $row[7],
            'coach' => $row[8],
            'status' => (string) $row[9],
            'address' => (string) $row[10],
            'linkedIn' => (string) $row[11],
            'function' => (string) $row[12],
            'previousFunction' => (string) $row[13],
            'seniorityDate' => empty($row[14]) ? null : DateTimeImmutable::createFromFormat('d/m/Y', $row[14])->format('c'),
            'programType' => (string) $row[15],
            'nFirstName' => (string) $row[16],
            'nLastName' => (string) $row[17],
            'nEmail' => (string) $row[18],
            'nPhone' => (string) $row[19],
            'userCodePostal' => (string) $row[20],
            'userDepartment' => (string) $row[21],
            'userCity' => (string) $row[22],
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
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        return $this->importUser->import($row, $create, true);
    }
}
