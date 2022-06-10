<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\GetAllDocumentsForCoach;
use App\Domain\Model\Document;
use App\Infrastructure\Annotations\Coach;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllDocumentsForCoachController extends AbstractController
{
    private GetAllDocumentsForCoach $getAllDocuments;

    public function __construct(GetAllDocumentsForCoach $getAllDocuments)
    {
        $this->getAllDocuments = $getAllDocuments;
    }

    /**
     * @return ResultIterator|Document[]
     *
     * @Coach
     * @Query
     * @Logged
     */
    public function getAllDocumentsForCoach(
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
        ?string $eventId = null,
        ?string $createdAt = null
    ): ResultIterator {
        /** @var ResultIterator|Document[] $result */
        $result = $this->getAllDocuments->getAll($search, $tagSearch, $visibility, $sortColumn, $sortDirection, $avoidProgram, $avoidEvent, $type, $displayedInHomePage, $avoidHidden, $categoryId, $signaturePending, $signedByCoach, $signedByCandidate, $livrableId, $programId, $eventId, $createdAt);

        return $result;
    }
}
