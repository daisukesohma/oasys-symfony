<?php

declare(strict_types=1);

namespace App\Application\Faq;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Question;
use App\Domain\Repository\QuestionRepository;

class DeleteQuestion
{
    private QuestionRepository $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    /**
     * @throws NotFound
     */
    public function delete(string $id): Question
    {
        $question = $this->questionRepository->mustFindOneById($id);

        $question->setDeleted(true);

        $this->questionRepository->save($question);

        return $question;
    }
}
