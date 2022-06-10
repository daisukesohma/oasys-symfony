<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\GetAllDocumentsForAdmin;
use App\Domain\Model\Document;
use App\Infrastructure\Annotations\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllDocumentsForAdminController extends AbstractController
{
    private GetAllDocumentsForAdmin $getAllDocuments;

    public function __construct(GetAllDocumentsForAdmin $getAllDocuments)
    {
        $this->getAllDocuments = $getAllDocuments;
    }

    /**
     * @return ResultIterator|Document[]
     *
     * @Admin
     * @Query
     * @Logged
     */
    public function getAllDocumentsForAdmin(
        ?string $search,
        ?string $tagSearch,
        ?string $visibility = null,
        ?string $sortColumn = 'createdAt',
        ?string $sortDirection = 'desc',
        ?string $avoidProgram = null,
        ?string $avoidEvent = null,
        ?string $type = null,
        ?bool $displayedInHomePage = null,
        ?bool $avoidHidden = false,
        ?string $categoryId = null,
        ?bool $signaturePending = null,
        ?bool $signedByCoach = null,
        ?bool $signedByCandidate = null,
        ?string $livrableId = null,
        ?string $programId = null,
        ?string $authorId = null,
        ?string $eventId = null,
        ?string $createdAt = null
    ): ResultIterator {
        /** @var ResultIterator|Document[] $result */
        $result = $this->getAllDocuments->getAll($search, $tagSearch, $visibility, $sortColumn, $sortDirection, $avoidProgram, $avoidEvent, $type, $displayedInHomePage, $avoidHidden, $categoryId, $signaturePending, $signedByCoach, $signedByCandidate, $livrableId, $programId, $authorId, $eventId, $createdAt);

        return $result;
    }
}
