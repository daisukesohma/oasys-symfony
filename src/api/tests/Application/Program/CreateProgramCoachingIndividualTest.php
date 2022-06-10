<?php

declare(strict_types=1);

namespace App\Tests\Application\Program;

use App\Application\Program\CreateProgramCoachingIndividual;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Model\ProgramModel;
use App\Domain\Model\User;
use App\Domain\Repository\ProgramModelRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use function array_map;

class CreateProgramCoachingIndividualTest extends ApplicationTestCase
{
    protected CreateProgramCoachingIndividual $createProgram;
    protected ProgramModel $programModel;
    /** @var User[] $users */
    protected array $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createProgram = self::$container->get(CreateProgramCoachingIndividual::class);

        $this->programModel = new ProgramModel($this->faker->name, $this->faker->text);
        self::$container->get(ProgramModelRepository::class)->save($this->programModel);

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::COACH);
        $user = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        self::$container->get(UserRepository::class)->save($user);
        $this->users[] = $user;
    }

    public function testCreateProgramCoachingIndividual(): void
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = $this->faker->email;
        $phoneNumber = $this->faker->phoneNumber;
        $name = $this->faker->name;
        $description = $this->faker->text;
        $type = ProgramTypeEnum::INDIVIDUAL;
        $userIds = array_map(static fn(User $user) => $user->getId(), $this->users);

        $programCoachingIndividual = $this->createProgram->create(
            $name,
            $description,
            $type,
            $userIds,
            $firstName,
            $lastName,
            $email,
            $phoneNumber,
            $userIds[0],
            $this->programModel->getId(),
        );

        $this->assertEquals($name, $programCoachingIndividual->getName());
        $this->assertEquals($description, $programCoachingIndividual->getDescription());
        $this->assertEquals($type, $programCoachingIndividual->getType());
        $this->assertEquals($userIds[0], $programCoachingIndividual->getCoaches()[0]->getId());
        $this->assertEquals($this->programModel->getId(), $programCoachingIndividual->getProgramModel()->getId());

        $this->assertEquals($firstName, $programCoachingIndividual->getFirstName());
        $this->assertEquals($lastName, $programCoachingIndividual->getLastName());
        $this->assertEquals($email, $programCoachingIndividual->getEmail());
        $this->assertEquals($phoneNumber, $programCoachingIndividual->getPhone());
    }
}
