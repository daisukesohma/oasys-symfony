<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Todo\CreateTodoItem;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Repository\ProgramRepository;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use function preg_quote;

class CreateTodoItemTest extends ApplicationTestCase
{
    private Program $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = new Program($this->faker->name, $this->faker->text, ProgramTypeEnum::INDIVIDUAL);
        self::$container->get(ProgramRepository::class)->save($this->program);
    }

    public function testCreateTodo(): void
    {
        $label = $this->faker->text(100);
        $todo = self::$container->get(CreateTodoItem::class)->create($label, $this->program->getId());
        $this->assertEquals($label, $todo->getLabel());
    }

    public function testInvalidProgram(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(Program::class) . '/');
        self::$container->get(CreateTodoItem::class)->create(Uuid::uuid1()->toString(), $this->faker->text);
    }
}
