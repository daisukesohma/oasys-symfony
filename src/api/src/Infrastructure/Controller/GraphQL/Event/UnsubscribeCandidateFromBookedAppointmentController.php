<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\UnsubscribeCandidateFromBookedAppointment;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class UnsubscribeCandidateFromBookedAppointmentController extends AbstractController
{
    private UnsubscribeCandidateFromBookedAppointment $unsubscribeCandidateFromBookedAppointment;

    public function __construct(UnsubscribeCandidateFromBookedAppointment $unsubscribeCandidateFromBookedAppointment)
    {
        $this->unsubscribeCandidateFromBookedAppointment = $unsubscribeCandidateFromBookedAppointment;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     */
    public function unsubscribeCandidateFromBookedAppointment(string $eventId, string $userId): Event
    {
        return $this->unsubscribeCandidateFromBookedAppointment->unsubscribeAppointment($eventId, $userId);
    }
}
