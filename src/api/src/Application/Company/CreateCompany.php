<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Application\File\UploadFile;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepository;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Psr\Http\Message\UploadedFileInterface;
use Safe\DateTimeImmutable;
use function explode;
use function intval;
use function Safe\parse_url;

final class CreateCompany
{
    private CompanyRepository $companyRepository;
    private UploadFile $uploadFile;

    public function __construct(CompanyRepository $companyRepository, UploadFile $uploadFile)
    {
        $this->companyRepository = $companyRepository;
        $this->uploadFile = $uploadFile;
    }

    /**
     * @throws Exist
     * @throws InvalidFileValue
     */
    public function create(string $name, ?string $salesforceLink = null, ?UploadedFileInterface $file = null, ?string $rootPath = null): Company
    {
        $companyNameCheck = $this->companyRepository->findOneByName($name);
        if ($companyNameCheck !== null) {
            throw new Exist(Company::class, [], true);
        }

        // Code generation
        $company = $this->companyRepository->findByFilters(null, 'createdAt', 'DESC')->first();
        $count = 1;
        $date = new DateTimeImmutable();
        $code = 'ENT' . $date->format('Y') . $date->format('dm') . '_' . $count;
        if ($company) {
            $countLast = explode('_', $company->getCode())[1];
            $count = intval($countLast) + 1;
            $code = 'ENT' . $date->format('Y') . $date->format('dm') . '_' . $count;
        }

        $company = new Company($name, $code);
        if ($salesforceLink !== null) {
            $company->setSalesforceLink($salesforceLink);
        }

        // Functions and Services XLSX Import
        if ($file !== null && $rootPath !== null) {
            $file = $this->uploadFile->upload($file, $rootPath);
            $upstream = parse_url($file->getUpstream());
            $company->setFunctionsServicesFile($file);

            $reader = new Xlsx();
            $spreadsheet = $reader->load($rootPath . $upstream['path']);
            $sheet = $spreadsheet->getActiveSheet();
            $index = 2;

            $functions = [];
            $services = [];
            while (true) {
                $row = $sheet->rangeToArray('A' . $index . ':Z' . $index);
                $row = $row[0] ?? null;

                if (empty($row) || empty($row[0])) {
                    break;
                }

                $functions[] = $row[0];
                $services[] = $row[1];

                $index++;
            }

            $company->setFunctions($functions);
            $company->setServices($services);
        }

        $this->companyRepository->save($company);

        return $company;
    }
}
