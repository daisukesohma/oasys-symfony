<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Faq;

use App\Application\Faq\UpdateQuestion;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Question;
use App\Infrastructure\Annotations\IsManagerFaq;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class UpdateQuestionController extends AbstractController
{
    private UpdateQuestion $updateQuestion;

    public function __construct(UpdateQuestion $updateQuestion)
    {
        $this->updateQuestion = $updateQuestion;
    }

    /**
     * @throws InvalidStringValue
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @IsManagerFaq
     */
    public function updateQuestion(string $id, string $question, string $response, string $theme, string $programId): Question
    {
        return $this->updateQuestion->update($id, $question, $response, $theme, $programId);
    }
}
