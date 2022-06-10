<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\CreateUserFromOfflineForm;
use App\Application\User\ExistingUserFromOfflineFormNotifier;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramPic;
use App\Domain\Model\Role;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\ProfessionalCategoryRepository;
use App\Domain\Repository\ProgramPicRepository;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use function assert;

final class CreateUserFromOfflineFormTest extends ApplicationTestCase
{
    private CreateUserFromOfflineForm $createUser;
    private ProgramPic $program;

    /** @var Role[] $roles */
    private array $roles;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUser = self::$container->get(CreateUserFromOfflineForm::class);

        $program = new ProgramPic($this->faker->name, $this->faker->text, ProgramTypeEnum::PIC);
        $program->setLinkId(Uuid::uuid4()->toString());
        self::$container->get(ProgramPicRepository::class)->save($program);

        $this->program = $program;
    }

    public function testCreateFromOfflineForm(): void
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = $this->faker->email;
        $phone = $this->faker->phoneNumber;
        $civility =  CivilityEnum::MISTER_CODE;

        $user = $this->createUser->create(
            $this->program->getLinkId(),
            $firstName,
            $lastName,
            $email,
            $phone,
            $civility,
        );

        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($phone, $user->getPhone());
        $this->assertEquals($civility, $user->getCivility());

        // Test for the email for the same user
        $mockNotifier = $this->getMockBuilder(ExistingUserFromOfflineFormNotifier::class)->getMock();
        $mockNotifier->expects($this->once())->method('notify');

        assert($mockNotifier instanceof ExistingUserFromOfflineFormNotifier || $mockNotifier instanceof MockObject);

        $createUserFromOfflineForm = new CreateUserFromOfflineForm(
            self::$container->get(UserRepository::class),
            self::$container->get(UserTypeRepository::class),
            $mockNotifier,
            self::$container->get(ProgramPicRepository::class),
            self::$container->get(ProfessionalCategoryRepository::class),
            self::$container->get(RoleRepository::class),
            self::$container->get(CompanyRepository::class)
        );

        $createUserFromOfflineForm->create(
            $this->program->getLinkId(),
            $firstName,
            $lastName,
            $email,
            $phone,
            $civility,
        );
    }

    public function testCreateFromOfflineFormLinkId(): void
    {
        $this->expectException(NotFound::class);

        $this->createUser->create(
            Uuid::uuid4()->toString(),
            $this->faker->firstName,
            $this->faker->lastName,
            $this->faker->email,
            $this->faker->phoneNumber,
            CivilityEnum::MISTER_CODE,
        );
    }
}
