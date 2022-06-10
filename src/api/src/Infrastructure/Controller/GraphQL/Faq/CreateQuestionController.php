<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Faq;

use App\Application\Faq\CreateQuestion;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Question;
use App\Infrastructure\Annotations\IsManagerFaq;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class CreateQuestionController extends AbstractController
{
    private CreateQuestion $createQuestion;

    public function __construct(CreateQuestion $createQuestion)
    {
        $this->createQuestion = $createQuestion;
    }

    /**
     * @throws NotFound
     *
     * @Logged
     * @Mutation
     * @IsManagerFaq
     */
    public function createQuestion(string $question, string $response, string $theme, string $programId): Question
    {
        return $this->createQuestion->create($question, $response, $theme, $programId);
    }
}
