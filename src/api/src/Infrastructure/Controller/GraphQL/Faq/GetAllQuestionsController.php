<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Faq;

use App\Application\Faq\GetAllQuestions;
use App\Domain\Model\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllQuestionsController extends AbstractController
{
    private GetAllQuestions $getAllQuestions;

    public function __construct(GetAllQuestions $getAllQuestions)
    {
        $this->getAllQuestions = $getAllQuestions;
    }

    /**
     * @return ResultIterator|Question[]
     *
     * @Logged
     * @Query
     */
    public function getAllQuestions(?string $search = null, ?string $theme, ?string $sortColumn = null, ?string $sortDirection = null): ResultIterator
    {
        /** @var ResultIterator|Question[] $result */
        $result = $this->getAllQuestions->getAll($search, $theme, $sortColumn, $sortDirection);

        return $result;
    }
}
