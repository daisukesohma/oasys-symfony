<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\ImportUsers;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\InvalidUsersXlsx;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;
use function Safe\ini_set;

final class ImportUsersController extends AbstractController
{
    private ImportUsers $importUsers;
    private EnvVarHelper $envVarHelper;

    public function __construct(ImportUsers $importUsers, EnvVarHelper $envVarHelper)
    {
        $this->importUsers = $importUsers;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @return User[]
     *
     * @throws InvalidUsersXlsx
     * @throws InvalidFileValue
     * @throws InvalidStringValue
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_USER")
     */
    public function importUsers(UploadedFileInterface $file): array
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        return $this->importUsers->import($file, $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH));
    }
}
