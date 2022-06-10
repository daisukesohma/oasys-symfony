<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\UpdateEventMemo;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Tests\Application\ApplicationTestCase;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use function preg_quote;

class UpdateEventMemoTest extends ApplicationTestCase
{
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();
        $eventRepository = self::$container->get(EventRepository::class);

        $this->event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $this->event->setOrganizer($this->loggedUser);
        $this->event->setDateEvent(DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $this->faker->dateTimeBetween('+1 day', '+30 days')->format('c')));
        $eventRepository->save($this->event);
    }

    public function testUpdateEventMemo(): void
    {
        $memo = $this->faker->text;
        self::$container->get(UpdateEventMemo::class)->update($this->event->getId(), $memo);
        $this->assertEquals($memo, $this->event->getMemo());
    }

    public function testUpdateEventInvalid(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(Event::class) . '/');
        self::$container->get(UpdateEventMemo::class)->update(Uuid::uuid1()->toString(), $this->faker->text);
    }
}
