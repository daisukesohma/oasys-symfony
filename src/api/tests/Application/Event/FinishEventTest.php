<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\EventFinishedNotifier;
use App\Application\Event\FinishEvent;
use App\Application\Program\FinishProgram;
use App\Domain\Enum\EventStatusEnum;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Model\Program;
use App\Domain\Repository\EventRepository;
use App\Domain\Util\Time;
use App\Tests\Application\ApplicationTestCase;
use ArrayIterator;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use TheCodingMachine\TDBM\AlterableResultIterator;
use function assert;

class FinishEventTest extends ApplicationTestCase
{
    use Time;

    private EventRepository $eventRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventRepository = self::$container->get(EventRepository::class);
    }

    public function testFinishEvent(): void
    {
        $id = Uuid::uuid1()->toString();
        $program = $this->getMockBuilder(Program::class)
            ->setConstructorArgs([$this->faker->name, $this->faker->text, ProgramTypeEnum::INDIVIDUAL])
            ->getMock();
        assert($program instanceof Program || $program instanceof MockObject);

        $event = $this->getMockBuilder(Event::class)
            ->setConstructorArgs([$this->faker->name, $this->faker->text, EventTypeEnum::ALLIANCE])
            ->getMock();
        assert($event instanceof Event || $event instanceof MockObject);
        $event->method('getId')->willReturn($id);
        $event->method('getDateEvent')->willReturn($this->getCurrentTime());
        $event->method('getProgram')->willReturn($program);
        $event->method('getStatus')->willReturn(EventStatusEnum::FINISHED);
        $event->expects($this->once())->method('setStatus')->with(EventStatusEnum::FINISHED);

        $futureEvent = new Event($this->faker->name, $this->faker->text, EventTypeEnum::ALLIANCE);
        $futureEvent->setDateEvent($this->getCurrentTime()->modify('+1 day'));

        $program->expects($this->never())->method('setStatus');
        $iterator = new AlterableResultIterator(new ArrayIterator([$event, $futureEvent]));
        $program->method('getEvents')->willReturn($iterator);

        $eventRepository = $this->getMockBuilder(EventRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        assert($eventRepository instanceof EventRepository || $eventRepository instanceof MockObject);
        $eventRepository->method('mustFindOneById')->with($id)->willReturn($event);

        $notifier = $this->getMockBuilder(EventFinishedNotifier::class)
            ->getMock();
        assert($notifier instanceof EventFinishedNotifier || $event instanceof MockObject);
        $notifier->method('notify');

        $finishEvent = new FinishEvent($eventRepository, $notifier, self::$container->get(FinishProgram::class));
        $finishEvent->finish($id);
    }

    public function testFinishEventInvalidModel(): void
    {
        $this->expectException(NotFound::class);
        self::$container->get(FinishEvent::class)->finish(Uuid::uuid1()->toString());
    }

    public function testFinishEventProgram(): void
    {
        $id = Uuid::uuid1()->toString();

        $event = $this->getMockBuilder(Event::class)
            ->setConstructorArgs([$this->faker->name, $this->faker->text, EventTypeEnum::ALLIANCE])
            ->getMock();
        assert($event instanceof Event || $event instanceof MockObject);
        $event->method('getId')->willReturn($id);
        $event->method('getDateEvent')->willReturn($this->getCurrentTime());
        $event->method('getStatus')->willReturn(EventStatusEnum::FINISHED);
        $event->expects($this->once())->method('setStatus')->with(EventStatusEnum::FINISHED);

        $eventRepository = $this->getMockBuilder(EventRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        assert($eventRepository instanceof EventRepository || $eventRepository instanceof MockObject);
        $eventRepository->method('mustFindOneById')->with($id)->willReturn($event);

        $notifier = $this->getMockBuilder(EventFinishedNotifier::class)
            ->getMock();
        assert($notifier instanceof EventFinishedNotifier || $event instanceof MockObject);
        $notifier->method('notify');

        $finishEvent = new FinishEvent($eventRepository, $notifier, self::$container->get(FinishProgram::class));
        $finishEvent->finish($id);
    }
}
