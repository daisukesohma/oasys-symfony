<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Model\Program;
use App\Tests\Domain\DomainTestCase;
use Safe\DateTimeImmutable;

final class ProgramTest extends DomainTestCase
{
    public function testSetProgramName(): void
    {
        $program = new Program($this->faker->name, $this->faker->text, ProgramTypeEnum::INDIVIDUAL);

        $name = $this->faker->name;
        $program->setName($name);
        $this->assertEquals($name, $program->getName());

        $this->expectException(InvalidStringValue::class);
        $program->setName('');

        $this->expectException(InvalidStringValue::class);
        $program->setName($this->faker->text(256));
    }

    public function testSetProgramDates(): void
    {
        $program = new Program($this->faker->name, $this->faker->text, ProgramTypeEnum::INDIVIDUAL);
        $dateStart = $this->faker->dateTimeBetween('now', '+30 days');
        $dateEnd = $this->faker->dateTimeBetween('+31 days', '+60 days');

        $program->setDateStart(\DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $dateStart->format('c')));
        $program->setDateEnd(\DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $dateEnd->format('c')));
        $this->assertEquals($dateStart->getTimestamp(), $program->getDateStart()->getTimestamp());
        $this->assertEquals($dateEnd->getTimestamp(), $program->getDateEnd()->getTimestamp());
    }

    public function testSetProgramType(): void
    {
        $program = new Program($this->faker->name, $this->faker->text, ProgramTypeEnum::INDIVIDUAL);

        $this->expectException(InvalidStringValue::class);
        $program->setType('random');
    }
}
