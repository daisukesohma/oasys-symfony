<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\Program\UpdateProgramEventDates;
use App\Domain\Enum\EventStatusEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;

final class DeleteEvent
{
    private EventRepository $eventRepository;
    private DeleteEventNotifier $deleteEventNotifier;
    private UpdateProgramEventDates $updateProgramEventDates;

    public function __construct(EventRepository $eventRepository, DeleteEventNotifier $deleteEventNotifier, UpdateProgramEventDates $updateProgramEventDates)
    {
        $this->eventRepository = $eventRepository;
        $this->deleteEventNotifier = $deleteEventNotifier;
        $this->updateProgramEventDates = $updateProgramEventDates;
    }

    /**
     * @throws NotFound
     */
    public function delete(string $id): Event
    {
        $event = $this->eventRepository->mustFindOneById($id);

        if ($event->getStatus() === EventStatusEnum::UPCOMING) {
            foreach ($event->getUsers() as $user) {
                $this->deleteEventNotifier->notify($event, $user);
            }
        }

        $programId = $event->getProgram() !== null ? $event->getProgram()->getId() : null;
        $this->eventRepository->delete($event);

        if ($programId !== null) {
            $this->updateProgramEventDates->update($programId);
        }

        return $event;
    }
}
