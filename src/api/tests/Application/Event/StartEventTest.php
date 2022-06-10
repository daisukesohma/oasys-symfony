<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\StartEvent;
use App\Application\Program\StartProgram;
use App\Domain\Enum\EventStatusEnum;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Domain\Util\Time;
use App\Tests\Application\ApplicationTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use function assert;

class StartEventTest extends ApplicationTestCase
{
    use Time;

    private EventRepository $eventRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventRepository = self::$container->get(EventRepository::class);
    }

    public function testStartEvent(): void
    {
        $id = Uuid::uuid1()->toString();

        $event = $this->getMockBuilder(Event::class)
            ->setConstructorArgs([$this->faker->name, $this->faker->text, EventTypeEnum::ALLIANCE])
            ->getMock();
        assert($event instanceof Event || $event instanceof MockObject);
        $event->method('getId')->willReturn($id);
        $event->method('getDateEvent')->willReturn($this->getCurrentTime());
        $event->expects($this->once())->method('setStatus')->with(EventStatusEnum::ONGOING);

        $eventRepository = $this->getMockBuilder(EventRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        assert($eventRepository instanceof EventRepository || $eventRepository instanceof MockObject);
        $eventRepository->method('mustFindOneById')->with($id)->willReturn($event);

        $startEvent = new StartEvent($eventRepository, self::$container->get(StartProgram::class));
        $startEvent->start($id);
    }

    public function testStartEventInvalidModel(): void
    {
        $this->expectException(NotFound::class);
        self::$container->get(StartEvent::class)->start(Uuid::uuid1()->toString());
    }

    public function testStartEventFutureEvent(): void
    {
        $id = Uuid::uuid1()->toString();

        $event = $this->getMockBuilder(Event::class)
            ->setConstructorArgs([$this->faker->name, $this->faker->text, EventTypeEnum::ALLIANCE])
            ->getMock();
        assert($event instanceof Event || $event instanceof MockObject);
        $event->method('getId')->willReturn($id);
        $event->method('getDateEvent')->willReturn($this->getCurrentTime()->modify('+1 day'));

        $eventRepository = $this->getMockBuilder(EventRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        assert($eventRepository instanceof EventRepository || $eventRepository instanceof MockObject);
        $eventRepository->method('mustFindOneById')->with($id)->willReturn($event);

        $this->expectException(InvalidDateValue::class);
        $startEvent = new StartEvent($eventRepository, self::$container->get(StartProgram::class));
        $startEvent->start($id);
    }
}
