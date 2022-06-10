<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Domain\Enum\RoleEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Model\User;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Infrastructure\Security\UserProvider;
use Doctrine\DBAL\Connection;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use function assert;
use function serialize;

abstract class ApplicationTestCase extends WebTestCase
{
    protected Generator $faker;
    protected Connection $dbal;
    protected User $loggedUser;
    private UserProvider $userProvider;
    private TokenStorageInterface $tokenStorage;
    private SessionInterface $session;

    private const ADMIN_FIRST_NAME = 'Datum';
    private const ADMIN_LAST_NAME = 'Data';
    private const ADMIN_EMAIL = 'test@oasys.localhost';
    private const ADMIN_PHONE = '0123456789';
    private const ADMIN_PASSWORD = 'Secret93';

    protected function setUp(): void
    {
        self::bootKernel();
        $this->faker = Factory::create('fr_FR');
        $this->dbal = self::$container->get(Connection::class);
        $this->userProvider = self::$container->get(UserProvider::class);
        $this->tokenStorage = self::$container->get(TokenStorageInterface::class);
        $this->session = self::$container->get(SessionInterface::class);
        if (self::$container->get(UserRepository::class)->findOneByEmail(self::ADMIN_EMAIL) === null) {
            $this->createAdminUser();
        }
        $this->dbal->beginTransaction();
        $this->logIn();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->dbal->rollBack();
        parent::tearDown();
    }

    private function logIn(): void
    {
        $user = $this->userProvider->loadUserByUsername(self::ADMIN_EMAIL);
        $this->loggedUser = self::$container->get(UserRepository::class)->mustFindOneById($user->getId());

        // Handle getting or creating the user entity likely with a posted form
        // The third parameter "main" can change according to the name of your firewall in security.yml
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);

        // If the firewall name is not main, then the set value would be instead:
        // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
        $this->session->set('_security_main', serialize($token));
    }

    private function createAdminUser(): void
    {
        $userRepository = self::$container->get(UserRepository::class);
        assert($userRepository instanceof UserRepository);
        $userTypeRepository = self::$container->get(UserTypeRepository::class);
        $roleRepository = self::$container->get(RoleRepository::class);
        assert($roleRepository instanceof RoleRepository);

        $type = $userTypeRepository->mustFindOneById(UserTypeEnum::ADMINISTRATOR);

        $user = new User($type, self::ADMIN_FIRST_NAME, self::ADMIN_LAST_NAME, self::ADMIN_EMAIL, self::ADMIN_PHONE);
        $user->setPassword(self::ADMIN_PASSWORD);

        $role = $roleRepository->mustFindOneById(RoleEnum::ADMINISTRATEUR_ID);
        $user->addRoleByUsersRoles($role);

        $userRepository->saveNoLog($user);
    }
}
