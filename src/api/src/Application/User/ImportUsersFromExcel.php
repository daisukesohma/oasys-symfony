<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Application\File\ImportFile;
use App\Domain\Exception\EmailError;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Exception\InvalidImportUser;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\InvalidUsersXlsx;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Psr\Http\Message\UploadedFileInterface;
use Safe\Exceptions\DatetimeException;
use function array_map;
use function array_merge;
use function count;
use function Safe\ini_set;
use function Safe\sprintf;

trait ImportUsersFromExcel
{
    use ImportFile;

    /** @var string[] */
    private static array $errors = [
        'duplicate_email' => 'Il y a deux lignes ou plus avec l\'e-mail "%s"',
        'datetime_invalid' => 'La valeur de date "%s" n\'est pas valide',
    ];

    /**
     * @return User[]
     *
     * @throws InvalidUsersXlsx
     * @throws InvalidFileValue
     * @throws InvalidStringValue
     * @throws NotFound
     */
    public function import(UploadedFileInterface $file, string $rootPath): array
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $reader = new Xlsx();
        $spreadsheet = $reader->load($this->importFile($file, $rootPath));
        $sheet = $spreadsheet->getActiveSheet();
        $index = 2;
        $errors = [];
        $users = [];
        $userEmailMap = [];

        // Do the first loop for error checking
        while (true) {
            $row = $sheet->rangeToArray('A' . $index . ':AZ' . $index);
            $row = $row[0] ?? [];

            if (empty($row) || empty($row[0]) || empty($row[5])) {
                break;
            }

            try {
                $row = $this->mapExcelRowToArray($row);

                // Check for duplicates within this file
                if (isset($userEmailMap[$row['email']])) {
                    throw new InvalidImportUser([sprintf(self::$errors['duplicate_email'], $row['email'])]);
                }

                $this->importUser($row, false);
                $users[] = $row;
                $userEmailMap[$row['email']] = count($users) - 1;
            } catch (InvalidImportUser $e) {
                $errors = array_merge($errors, array_map(static fn(string $error) => [
                    'line' => $index,
                    'message' => $error,
                ], $e->getErrors()));
            } catch (DatetimeException $e) {
                $errors[] = [
                    'line' => $index,
                    'message' => sprintf(self::$errors['datetime_invalid'], $row[14]),
                ];
            }
            $index++;
        }

        $return = [];
        if (! empty($errors)) {
            throw new InvalidUsersXlsx($errors);
        } else {
            foreach ($users as $user) {
                $user = $this->importUser($user);
                if ($user === null) {
                    continue;
                }

                $return[] = $user;
            }
        }

        return $return;
    }

    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    abstract protected function mapExcelRowToArray(array $row): array;

    /**
     * @param mixed[] $row
     *
     * @throws DatetimeException
     * @throws EmailError
     */
    abstract protected function importUser(array $row, bool $create = true): ?User;
}
