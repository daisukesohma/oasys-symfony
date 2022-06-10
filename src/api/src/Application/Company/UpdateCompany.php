<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Application\File\DeleteFile;
use App\Application\File\UploadFile;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepository;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Psr\Http\Message\UploadedFileInterface;
use function Safe\parse_url;

final class UpdateCompany
{
    private CompanyRepository $companyRepository;
    private UploadFile $uploadFile;
    private DeleteFile $deleteFile;

    public function __construct(CompanyRepository $companyRepository, UploadFile $uploadFile, DeleteFile $deleteFile)
    {
        $this->companyRepository = $companyRepository;
        $this->uploadFile = $uploadFile;
        $this->deleteFile = $deleteFile;
    }

    /**
     * @throws NotFound
     * @throws InvalidStringValue
     * @throws Exist
     * @throws InvalidFileValue
     */
    public function update(string $id, string $name, ?string $salesforceLink = null, ?UploadedFileInterface $file = null, ?string $rootPath = null): Company
    {
        $company = $this->companyRepository->mustFindOneById($id);

        if ($name !== $company->getName()) {
            $companyNameCheck = $this->companyRepository->findOneByName($name);
            if ($companyNameCheck !== null) {
                throw new Exist(Company::class, [], true);
            }
        }

        $company->setName($name);
        $company->setSalesforceLink($salesforceLink === '' ? null : $salesforceLink);

        // Functions and Services XLSX Import
        $existingFile = null;
        if ($file !== null && $rootPath !== null) {
            $existingFile = $company->getFunctionsServicesFile();
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

        if ($existingFile !== null && $rootPath !== null) {
            $this->deleteFile->delete($existingFile->getId(), $rootPath);
        }

        return $company;
    }
}
