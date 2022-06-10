<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Faq;

use App\Application\Faq\DeleteQuestion;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Question;
use App\Infrastructure\Annotations\IsManagerFaq;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class DeleteQuestionController extends AbstractController
{
    private DeleteQuestion $deleteQuestion;

    public function __construct(DeleteQuestion $deleteQuestion)
    {
        $this->deleteQuestion = $deleteQuestion;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @IsManagerFaq
     */
    public function deleteQuestion(
        string $id
    ): Question {
        return $this->deleteQuestion->delete($id);
    }
}
