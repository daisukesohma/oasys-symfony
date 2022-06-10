<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\UserRepository;
use DateTimeZone;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Eluceo\iCal\Property\Event\Organizer;
use Safe\DateTimeImmutable;
use function Safe\substr;
use function strlen;
use function strtoupper;

final class CreateCalendarFile
{
    private EventRepository $eventRepository;
    private UserRepository $userRepository;

    public function __construct(EventRepository $eventRepository, UserRepository $userRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws NotFound
     */
    public function create(string $eventId, string $hostUrl, ?User $candidate = null): string
    {
        $hostUrl = substr($hostUrl, -1) === '/' ? substr($hostUrl, 0, strlen($hostUrl) - 1) : $hostUrl;
        $event = $this->eventRepository->mustFindOneById($eventId);
        $vCalendar = new Calendar($hostUrl);
        $vEvent = new Event();
        $dateStart = $event->getDateEvent() ? $event->getDateEvent() : new DateTimeImmutable();
        $dateEnd = $event->getDateEventEnd() ? $event->getDateEventEnd() : new DateTimeImmutable();
        $eventType = $event->getProgram() !==null ? $event->getProgram()->getType() : '';
        $timezone = new DateTimeZone('Europe/Paris');
        $dateStart = $dateStart ? (new DateTimeImmutable($dateStart->format('Y-m-d H:i:s'), $timezone)) : null;
        $dateEnd = $dateEnd ? (new DateTimeImmutable($dateEnd->format('Y-m-d H:i:s'), $timezone)) : null;

        $vEvent
            ->setUniqueId($event->getId() . '@' . $hostUrl)
            ->setDtStart($dateStart)
            ->setDtEnd($dateEnd)
            ->setSummary($event->getName())
            ->setDescription($event->getDescription())
            ->setLocation($event->getMeetingRoom())
            ->setUrl($hostUrl . '/candidate-dashboard?eventId=' . $event->getId());

        $organizer = $event->getOrganizer();
        if ($organizer !== null) {
            $vEvent->setOrganizer(new Organizer($organizer->getEmail()));
            $vEvent->setSummary($organizer->getFirstName() . ' ' . strtoupper($organizer->getLastName()));
        }
        if ($candidate !== null) {
            $vEvent->setSummary($candidate->getFirstName() . ' ' . strtoupper($candidate->getLastName()));
        }

        $vCalendar->addComponent($vEvent);

        return $vCalendar->render();
    }
}
