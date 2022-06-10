<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\NotFound;
use App\Domain\Model\EventRate;
use App\Domain\Repository\EventRateRepository;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\UserRepository;
use Safe\DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

final class RateEvent
{
    private EventRepository $eventRepository;
    private EventRateRepository $eventRateRepository;
    private UserRepository $userRepository;

    public function __construct(EventRepository $eventRepository, EventRateRepository $eventRateRepository, UserRepository $userRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->eventRateRepository = $eventRateRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws NotFound
     */
    public function rate(string $eventId, int $starsNumber, string $comment): EventRate
    {
        $event = $this->eventRepository->mustFindOneById($eventId);
        $rateEvent = $this->eventRateRepository->findOneByEventAndUser($event, $this->userRepository->getLoggedUser());

        if ($rateEvent !== null) {
            throw new AccessDeniedException('Event is already rated');
        }

        $rateEvent = new EventRate($event, $this->userRepository->getLoggedUser(), $starsNumber);

        $rateEvent->setStarsNumber($starsNumber);
        $rateEvent->setRateNote($comment);
        $rateEvent->setCreatedAt(new DateTimeImmutable());

        $this->eventRateRepository->save($rateEvent);

        return $rateEvent;
    }
}
