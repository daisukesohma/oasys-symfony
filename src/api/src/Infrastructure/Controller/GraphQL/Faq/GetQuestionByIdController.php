<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Faq;

use App\Application\Faq\GetQuestionById;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetQuestionByIdController extends AbstractController
{
    private GetQuestionById $getQuestionById;

    public function __construct(GetQuestionById $getQuestionById)
    {
        $this->getQuestionById = $getQuestionById;
    }

    /**
     * @throws NotFound
     *
     * @Query
     * @Logged
     */
    public function getQuestionById(string $id): Question
    {
        return $this->getQuestionById->get($id);
    }
}
