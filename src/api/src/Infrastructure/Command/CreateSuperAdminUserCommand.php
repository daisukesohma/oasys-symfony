<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Infrastructure\Config\EnvVarHelper;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateSuperAdminUserCommand extends Command
{
    use CreateUser;

    private Connection $dbal;
    private EnvVarHelper $envVarHelper;
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private UserTypeRepository $userTypeRepository;

    public function __construct(Connection $dbal, EnvVarHelper $envVarHelper, UserRepository $userRepository, RoleRepository $roleRepository, UserTypeRepository $userTypeRepository)
    {
        $this->dbal = $dbal;
        $this->envVarHelper = $envVarHelper;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->userTypeRepository = $userTypeRepository;
        parent::__construct('users:create-super-admin');
    }

    public function configure(): void
    {
        $this->setDescription('Creates the super admin user.')
            ->setHelp('This command allows you to create the super admin user according to environment variables.');
    }

    /**
     * @throws InvalidStringValue
     * @throws NotFound
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->create(
            $output,
            $this->envVarHelper->fetch(EnvVarHelper::API_SUPER_ADMIN_FIRST_NAME),
            $this->envVarHelper->fetch(EnvVarHelper::API_SUPER_ADMIN_LAST_NAME),
            $this->envVarHelper->fetch(EnvVarHelper::API_SUPER_ADMIN_PHONE),
            $this->envVarHelper->fetch(EnvVarHelper::API_SUPER_ADMIN_EMAIL),
            $this->envVarHelper->fetch(EnvVarHelper::API_SUPER_ADMIN_PASSWORD),
        );

        return 0;
    }
}
