<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\DeleteDocument;
use App\Domain\Exception\InvalidRight;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class DeleteDocumentController extends AbstractController
{
    private DeleteDocument $deleteDocument;

    public function __construct(DeleteDocument $deleteDocument)
    {
        $this->deleteDocument = $deleteDocument;
    }

    /**
     * @throws NotFound
     * @throws InvalidRight
     *
     * @Mutation
     * @Logged
     */
    public function deleteDocument(
        string $id
    ): Document {
        return $this->deleteDocument->delete($id);
    }
}
