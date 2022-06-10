<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Todo\MarkTodoAsDone;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Model\Todo;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\TodoRepository;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use function preg_quote;

class MarkTodoAsDoneTest extends ApplicationTestCase
{
    private Todo $todo;

    protected function setUp(): void
    {
        parent::setUp();
        $program = new Program($this->faker->name, $this->faker->text, ProgramTypeEnum::INDIVIDUAL);
        self::$container->get(ProgramRepository::class)->save($program);

        $this->todo = new Todo($program, $this->faker->name);
        self::$container->get(TodoRepository::class)->save($this->todo);
    }

    public function testMarkTodoAsDone(): void
    {
        self::$container->get(MarkTodoAsDone::class)->mark($this->todo->getId());
        $this->assertTrue($this->todo->getDone());
    }

    public function testInvalidTodo(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(Todo::class) . '/');
        self::$container->get(MarkTodoAsDone::class)->mark(Uuid::uuid1()->toString());
    }
}
