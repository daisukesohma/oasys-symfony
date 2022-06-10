<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Company;

use App\Application\Company\CreateCompany;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Model\Company;
use App\Infrastructure\Config\EnvVarHelper;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateCompanyController extends AbstractController
{
    private CreateCompany $createCompany;
    private EnvVarHelper $envVarHelper;

    public function __construct(CreateCompany $createCompany, EnvVarHelper $envVarHelper)
    {
        $this->createCompany = $createCompany;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @throws Exist
     * @throws InvalidFileValue
     *
     * @Logged
     * @Mutation
     * @Right("ROLE_CREATE_COMPANY")
     */
    public function createCompany(string $name, ?string $salesforceLink = null, ?UploadedFileInterface $file = null): Company
    {
        return $this->createCompany->create($name, $salesforceLink, $file, $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH));
    }
}
