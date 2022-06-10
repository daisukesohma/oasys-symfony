<?php

declare(strict_types=1);

namespace App\Tests\Application\Program;

use App\Application\Program\UpdateProgram;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Model\ProgramCoachingIndividual;
use App\Domain\Model\ProgramModel;
use App\Domain\Model\User;
use App\Domain\Repository\ProgramCoachingIndividualRepository;
use App\Domain\Repository\ProgramModelRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use function array_map;

class UpdateProgramCoachingIndividualTest extends ApplicationTestCase
{
    protected ProgramCoachingIndividual $programCoachingIndividual;
    protected UpdateProgram $updateProgram;
    protected ProgramModel $programModel;
    /** @var User[] $users */
    protected array $users;

    protected function setUp(): void
    {
        parent::setUp();
        $programCoachingIndividualRepository = self::$container->get(ProgramCoachingIndividualRepository::class);

        $this->updateProgram = self::$container->get(UpdateProgram::class);

        $programModelRepository = self::$container->get(ProgramModelRepository::class);

        $this->programModel = new ProgramModel($this->faker->name, $this->faker->text);
        $programModelRepository->save($this->programModel);

        $this->programCoachingIndividual = new ProgramCoachingIndividual($this->faker->name, $this->faker->text, ProgramTypeEnum::INDIVIDUAL, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        $this->programCoachingIndividual->setProgramModel($this->programModel);
        $programCoachingIndividualRepository->save($this->programCoachingIndividual);

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::COACH);
        $user = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        self::$container->get(UserRepository::class)->save($user);

        $this->users[] = $user;
    }

    public function testCreateProgramCoachingIndividual(): void
    {
        $name = $this->faker->name;
        $description = $this->faker->text;
        $type = ProgramTypeEnum::INDIVIDUAL;
        $userIds = array_map(static fn(User $user) => $user->getId(), $this->users);

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = $this->faker->email;
        $phoneNumber = $this->faker->phoneNumber;

        $programCoachingIndividual = $this->updateProgram->update(
            $this->programCoachingIndividual->getId(),
            $name,
            $description,
            $type,
            $userIds,
            [$userIds[0]],
            $this->programModel->getId(),
            $firstName,
            $lastName,
            $email,
            $phoneNumber,
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
