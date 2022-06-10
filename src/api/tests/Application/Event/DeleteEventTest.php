<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\DeleteEvent;
use App\Application\Event\DeleteEventNotifier;
use App\Application\Program\UpdateProgramEventDates;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\ProgramRepository;
use App\Tests\Application\ApplicationTestCase;
use DateTimeImmutable;
use function preg_quote;

class DeleteEventTest extends ApplicationTestCase
{
    protected EventRepository $eventRepository;
    protected ProgramRepository $programRepository;
    protected DeleteEvent $deleteEvent;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $mockNotifier = $this->getMockBuilder(DeleteEventNotifier::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventRepository = self::$container->get(EventRepository::class);
        $this->programRepository = self::$container->get(ProgramRepository::class);
        $this->deleteEvent = new DeleteEvent(self::$container->get(EventRepository::class), $mockNotifier, new UpdateProgramEventDates($this->programRepository, $this->eventRepository));

        $this->event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $this->event->setOrganizer($this->loggedUser);
        $this->event->setDateEvent(DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $this->faker->dateTimeBetween('+1 day', '+30 days')->format('c')));
        $this->eventRepository->save($this->event);
    }

    public function testDeleteEvent(): void
    {
        $eventId = $this->event->getId();
        $this->deleteEvent->delete($eventId);

        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(Event::class) . '/i');
        $this->eventRepository->mustFindOneById($eventId);
    }
}
