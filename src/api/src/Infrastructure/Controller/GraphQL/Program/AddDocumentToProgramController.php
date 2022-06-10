<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\AddDocumentToProgram;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Program;
use App\Infrastructure\Config\EnvVarHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class AddDocumentToProgramController extends AbstractController
{
    private AddDocumentToProgram $addDocument;
    private EnvVarHelper $envVarHelper;

    public function __construct(AddDocumentToProgram $addDocument, EnvVarHelper $envVarHelper)
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
    public function addDocumentToProgram(string $programId, string $documentId): Program
    {
        return $this->addDocument->add($programId, $documentId, $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH), $this->envVarHelper->fetch(EnvVarHelper::WEBHOOK_YOUSIGN_URL));
    }
}
