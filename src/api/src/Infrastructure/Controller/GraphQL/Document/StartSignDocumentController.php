<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\InviteUserToSignDocument;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\EmailError;
use App\Domain\Exception\NotFound;
use App\Domain\Model\DocumentSigner;
use App\Infrastructure\Security\SerializableUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\TDBM\ResultIterator;

final class StartSignDocumentController extends AbstractController
{
    private InviteUserToSignDocument $startSignDocument;

    public function __construct(InviteUserToSignDocument $startSignDocument)
    {
        $this->startSignDocument = $startSignDocument;
    }

    /**
     * @return ResultIterator|DocumentSigner[]
     *
     * @throws NotFound
     * @throws EmailError
     *
     * @Mutation
     * @Logged
     * @InjectUser(for = "$user")
     * @Right("ROLE_UPDATE_DOCUMENT")
     */
    public function startSignDocument(
        string $id,
        SerializableUser $user
    ): ?ResultIterator {
        if ($user->getType() !== UserTypeEnum::COACH) {
            return null;
        }

        return $this->startSignDocument->startSignDocument($id);
    }
}
