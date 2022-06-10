<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\CreateDocumentFromEvent;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Document;
use App\Infrastructure\Config\EnvVarHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateDocumentFromEventController extends AbstractController
{
    private CreateDocumentFromEvent $createDocumentFromEvent;
    private EnvVarHelper $envVarHelper;

    public function __construct(CreateDocumentFromEvent $createDocumentFromEvent, EnvVarHelper $envVarHelper)
    {
        $this->createDocumentFromEvent = $createDocumentFromEvent;
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
    public function createDocumentFromEvent(
        string $name,
        string $description,
        string $tags,
        ?string $fileDescriptorId,
        string $authorId,
        string $categoryId,
        string $eventId,
        bool $toSign,
        ?string $elaborationDate = null,
        bool $hidden = false,
        ?string $type = null,
        ?string $articleLink = null,
        bool $toBeDisplayedInHomePage = false,
        ?string $livrableId = null
    ): Document {
        return $this->createDocumentFromEvent->create(
            $name,
            $description,
            $tags,
            $fileDescriptorId,
            $authorId,
            $categoryId,
            $eventId,
            $toSign,
            $this->envVarHelper->fetch(EnvVarHelper::WEBHOOK_YOUSIGN_URL),
            $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH),
            $elaborationDate,
            $hidden,
            $type,
            $articleLink,
            $toBeDisplayedInHomePage,
            $livrableId
        );
    }
}
