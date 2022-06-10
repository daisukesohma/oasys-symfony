<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Model\User;
use App\Domain\Repository\EventRepository;

final class NotifyEventEvaluationSurvey
{
    private EventRepository $eventRepository;
    private EventEvaluationSurveyNotifier $eventEvaluationSurveyNotifier;

    public function __construct(EventRepository $eventRepository, EventEvaluationSurveyNotifier $eventEvaluationSurveyNotifier)
    {
        $this->eventRepository = $eventRepository;
        $this->eventEvaluationSurveyNotifier = $eventEvaluationSurveyNotifier;
    }

    /**
     * @throws NotFound
     */
    public function notify(string $eventId, string $userId): Event
    {
        $event = $this->eventRepository->mustFindOneById($eventId);
        if ($event->getType() !== EventTypeEnum::TRIPARTITE) {
            throw new NotFound(Event::class, ['id' => $eventId]);
        }

        $user = null;
        foreach ($event->getUsers() as $emailUser) {
            if ($emailUser->getId() !== $userId) {
                continue;
            }

            $user = $emailUser;
        }
        if ($user === null) {
            throw new NotFound(User::class, ['id' => $userId, 'eventId' => $eventId]);
        }

        $this->eventEvaluationSurveyNotifier->notify($event, $user);

        return $event;
    }
}
