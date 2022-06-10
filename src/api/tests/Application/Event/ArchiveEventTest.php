<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\ArchiveEvent;
use App\Domain\Enum\EventStatusEnum;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Tests\Application\ApplicationTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Safe\DateTimeImmutable;
use function assert;

class ArchiveEventTest extends ApplicationTestCase
{
    private EventRepository $eventRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventRepository = self::$container->get(EventRepository::class);
    }

    public function testEndEvent(): void
    {
        $id = Uuid::uuid1()->toString();

        $event = $this->getMockBuilder(Event::class)
            ->setConstructorArgs([$this->faker->name, $this->faker->text, EventTypeEnum::ALLIANCE])
            ->getMock();
        assert($event instanceof Event || $event instanceof MockObject);
        $event->method('getId')->willReturn($id);
        $event->method('getStatus')->willReturn(EventStatusEnum::FINISHED);
        $event->method('getDateEvent')->willReturn((new DateTimeImmutable())->modify('-1 hour'));
        $event->expects($this->once())->method('setStatus')->with(EventStatusEnum::ARCHIVED);

        $eventRepository = $this->getMockBuilder(EventRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        assert($eventRepository instanceof EventRepository || $eventRepository instanceof MockObject);
        $eventRepository->method('mustFindOneById')->with($id)->willReturn($event);

        $endEvent = new ArchiveEvent($eventRepository);
        $endEvent->archive($id);
    }

    public function testEndEventInvalidModel(): void
    {
        $this->expectException(NotFound::class);
        self::$container->get(ArchiveEvent::class)->archive(Uuid::uuid1()->toString());
    }

    public function testEndEventFutureEvent(): void
    {
        $event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::ALLIANCE);
        $event->setDateEvent((new DateTimeImmutable())->modify('+1 day'));
        $this->eventRepository->save($event);

        $this->expectException(InvalidDateValue::class);
        self::$container->get(ArchiveEvent::class)->archive($event->getId());
    }
}
