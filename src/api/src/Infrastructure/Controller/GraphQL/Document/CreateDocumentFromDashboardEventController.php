<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\CreateDocumentFromEvent;
use App\Domain\Enum\DocumentCategoryEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Document;
use App\Infrastructure\Config\EnvVarHelper;
use App\Infrastructure\Security\SerializableUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class CreateDocumentFromDashboardEventController extends AbstractController
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
     * @InjectUser(for="$user")
     */
    public function createDocumentFromDashboardEvent(
        SerializableUser $user,
        string $name,
        ?string $fileDescriptorId,
        string $eventId,
        ?string $elaborationDate = null,
        ?string $livrableId = null
    ): Document {
        return $this->createDocumentFromEvent->create(
            $name,
            'Créé à partir de la vue Candidat',
            '',
            $fileDescriptorId,
            $user->getId(),
            DocumentCategoryEnum::CUSTOM,
            $eventId,
            false,
            $this->envVarHelper->fetch(EnvVarHelper::WEBHOOK_YOUSIGN_URL),
            $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH),
            $elaborationDate,
            true,
            'file',
            null,
            false,
            $livrableId
        );
    }
}
