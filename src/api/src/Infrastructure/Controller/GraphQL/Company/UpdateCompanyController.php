<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Company;

use App\Application\Company\UpdateCompany;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use App\Infrastructure\Config\EnvVarHelper;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class UpdateCompanyController extends AbstractController
{
    private UpdateCompany $updateCompany;
    private EnvVarHelper $envVarHelper;

    public function __construct(UpdateCompany $updateCompany, EnvVarHelper $envVarHelper)
    {
        $this->updateCompany = $updateCompany;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @throws InvalidStringValue
     * @throws InvalidFileValue
     * @throws Exist
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_UPDATE_COMPANY")
     */
    public function updateCompany(string $id, string $name, ?string $salesforceLink = null, ?UploadedFileInterface $file = null): Company
    {
        return $this->updateCompany->update($id, $name, $salesforceLink, $file, $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH));
    }
}
