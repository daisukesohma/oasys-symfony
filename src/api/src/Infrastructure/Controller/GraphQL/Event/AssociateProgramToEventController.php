<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\AssociateProgramToEvent;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class AssociateProgramToEventController extends AbstractController
{
    private AssociateProgramToEvent $associateProgramToEvent;

    public function __construct(AssociateProgramToEvent $associateProgramToEvent)
    {
        $this->associateProgramToEvent = $associateProgramToEvent;
    }

    /**
     * @param string[] $eventIds
     *
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_PROGRAM")
     */
    public function associateProgramToEvent(string $programId, array $eventIds): Program
    {
        return $this->associateProgramToEvent->associate($programId, $eventIds);
    }
}
