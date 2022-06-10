<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;

final class UpdateEventMemo
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @throws NotFound
     */
    public function update(string $id, string $memo): Event
    {
        $event = $this->eventRepository->mustFindOneById($id);
        $event->setMemo($memo);

        $this->eventRepository->save($event);

        return $event;
    }
}
