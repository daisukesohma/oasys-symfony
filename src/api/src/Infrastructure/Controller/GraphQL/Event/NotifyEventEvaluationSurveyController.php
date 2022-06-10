<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\NotifyEventEvaluationSurvey;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class NotifyEventEvaluationSurveyController extends AbstractController
{
    private NotifyEventEvaluationSurvey $notifyEventEvaluationSurvey;

    public function __construct(NotifyEventEvaluationSurvey $notifyEventEvaluationSurvey)
    {
        $this->notifyEventEvaluationSurvey = $notifyEventEvaluationSurvey;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     */
    public function notifyEventEvaluationSurvey(string $eventId, string $userId): Event
    {
        return $this->notifyEventEvaluationSurvey->notify($eventId, $userId);
    }
}
