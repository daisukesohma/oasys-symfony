<?php

declare(strict_types=1);

namespace App\Tests\Application\Program;

use App\Application\Program\DeleteProgram;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Model\Program;
use App\Domain\Model\ProgramCoachingIndividual;
use App\Domain\Repository\ProgramCoachingIndividualRepository;
use App\Domain\Repository\ProgramRepository;
use App\Tests\Application\ApplicationTestCase;
use DateTimeImmutable;

class DeleteProgramTest extends ApplicationTestCase
{
    protected DeleteProgram $deleteProgram;
    protected Program $program;
    protected ProgramCoachingIndividual $programCoachingIndividual;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteProgram = self::$container->get(DeleteProgram::class);
        $programRepository = self::$container->get(ProgramRepository::class);
        $programCoachingIndividualRepository = self::$container->get(ProgramCoachingIndividualRepository::class);

        $this->program = new Program($this->faker->name, $this->faker->text, ProgramTypeEnum::GROUP);
        $this->program->setDateStart(DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $this->faker->dateTimeBetween('now', '+30 days')->format('c')));
        $this->program->setDateEnd(DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $this->faker->dateTimeBetween('+31 days', '+60 days')->format('c')));
        $programRepository->save($this->program);

        $this->programCoachingIndividual = new ProgramCoachingIndividual($this->faker->name, $this->faker->text, ProgramTypeEnum::INDIVIDUAL, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        $programCoachingIndividualRepository->save($this->programCoachingIndividual);
    }

    public function testDeleteProgram(): void
    {
        $programId = $this->program->getId();
        $this->deleteProgram->delete($programId);
        $this->assertTrue($this->program->getDeleted());

        $programId = $this->programCoachingIndividual->getId();
        $this->deleteProgram->delete($programId);
        $this->assertTrue($this->programCoachingIndividual->getDeleted());
    }
}
