<?php

declare(strict_types=1);

namespace App\Tests\Application\Program;

use App\Application\Program\ArchiveProgram;
use App\Domain\Enum\ProgramStatusEnum;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Repository\ProgramRepository;
use App\Tests\Application\ApplicationTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use function assert;

class ArchiveProgramTest extends ApplicationTestCase
{
    private ProgramRepository $programRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programRepository = self::$container->get(ProgramRepository::class);
    }

    public function testEndProgram(): void
    {
        $id = Uuid::uuid1()->toString();

        $program = $this->getMockBuilder(Program::class)
            ->setConstructorArgs([$this->faker->name, $this->faker->text, ProgramTypeEnum::INDIVIDUAL])
            ->getMock();
        assert($program instanceof Program || $program instanceof MockObject);
        $program->method('getId')->willReturn($id);
        $program->method('getStatus')->willReturn(ProgramStatusEnum::FINISHED);
        $program->expects($this->once())->method('setStatus')->with(ProgramStatusEnum::ARCHIVED);

        $programRepository = $this->getMockBuilder(ProgramRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        assert($programRepository instanceof ProgramRepository || $programRepository instanceof MockObject);
        $programRepository->method('mustFindOneById')->with($id)->willReturn($program);

        $endProgram = new ArchiveProgram($programRepository);
        $endProgram->archive($id);
    }

    public function testEndProgramInvalidModel(): void
    {
        $this->expectException(NotFound::class);
        self::$container->get(ArchiveProgram::class)->archive(Uuid::uuid1()->toString());
    }

    public function testEndProgramInvalidStatus(): void
    {
        $program = new Program($this->faker->name, $this->faker->text, ProgramTypeEnum::INDIVIDUAL);
        $program->setStatus(ProgramStatusEnum::UPCOMING);
        $this->programRepository->save($program);

        $this->expectException(InvalidDateValue::class);
        self::$container->get(ArchiveProgram::class)->archive($program->getId());
    }
}
