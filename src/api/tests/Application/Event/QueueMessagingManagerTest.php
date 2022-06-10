<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Queue\QueueMessagingManager;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Tests\Application\ApplicationTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use RedisClient\RedisClient;
use Safe\DateTimeImmutable;
use function assert;

class QueueMessagingManagerTest extends ApplicationTestCase
{
    private EventRepository $eventRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventRepository = self::$container->get(EventRepository::class);
    }

    public function testQueueMessagingManager(): void
    {
        $eventTime = (new DateTimeImmutable())->modify('+1 day');
        $event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::ALLIANCE);
        $event->setDateEvent($eventTime);
        $event->setDateEventEnd($eventTime->modify('+1 hour'));
        $this->eventRepository->save($event);

        $redisMock = $this->getMockBuilder(RedisClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        assert($redisMock instanceof RedisClient || $redisMock instanceof MockObject);
        $redisMock->expects($this->any())->method('zadd');

        $queueMessagingManager = new QueueMessagingManager($this->eventRepository, $redisMock);
        $queueMessagingManager->queue($event->getId());
    }

    public function testQueueMessagingManagerInvalidEvent(): void
    {
        $this->expectException(NotFound::class);
        self::$container->get(QueueMessagingManager::class)->queue(Uuid::uuid1()->toString());
    }

    public function testQueueMessagingManagerInvalidDateEvent(): void
    {
        $event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::ALLIANCE);
        $this->eventRepository->save($event);

        $this->expectException(InvalidDateValue::class);
        self::$container->get(QueueMessagingManager::class)->queue($event->getId());
    }
}
