<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\AddDocumentToEvent;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Event;
use App\Infrastructure\Config\EnvVarHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class AddDocumentToEventController extends AbstractController
{
    private AddDocumentToEvent $addDocument;
    private EnvVarHelper $envVarHelper;

    public function __construct(AddDocumentToEvent $addDocument, EnvVarHelper $envVarHelper)
    {
        $this->addDocument = $addDocument;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @throws NotFound
     * @throws YouSignError
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_DOCUMENT")
     */
    public function addDocumentToEvent(string $eventId, string $documentId): Event
    {
        return $this->addDocument->add($eventId, $documentId, $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH), $this->envVarHelper->fetch(EnvVarHelper::WEBHOOK_YOUSIGN_URL));
    }
}
