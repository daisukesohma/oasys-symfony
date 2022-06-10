<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\GetDocumentsLivrable;
use App\Domain\Model\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\TDBM\ResultIterator;

final class GetDocumentsLivrableController extends AbstractController
{
    private GetDocumentsLivrable $getDocumentsLivrable;

    public function __construct(GetDocumentsLivrable $getDocumentsLivrable)
    {
        $this->getDocumentsLivrable = $getDocumentsLivrable;
    }

    /**
     * @return ResultIterator|Document[]
     *
     * @Query
     * @Logged
     * @Right("ROLE_CREATE_DOCUMENT")
     */
    public function getDocumentsLivrable(string $search, ?string $programId = null): ResultIterator
    {
        /** @var ResultIterator|Document[] $result */
        $result = $this->getDocumentsLivrable->getAll($search, $programId);

        return $result;
    }
}
