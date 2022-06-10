<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\BookAppointmentByCandidate;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class BookAppointmentByCandidateController extends AbstractController
{
    private BookAppointmentByCandidate $bookAppointmentByCandidate;

    public function __construct(BookAppointmentByCandidate $bookAppointmentByCandidate)
    {
        $this->bookAppointmentByCandidate = $bookAppointmentByCandidate;
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     *
     * @Mutation
     * @Logged
     */
    public function bookAppointmentByCandidate(string $eventId, string $userId): Event
    {
        return $this->bookAppointmentByCandidate->bookAppointment($eventId, $userId);
    }
}
