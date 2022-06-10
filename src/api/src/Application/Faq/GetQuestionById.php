<?php

declare(strict_types=1);

namespace App\Application\Faq;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Question;
use App\Domain\Repository\QuestionRepository;

final class GetQuestionById
{
    private QuestionRepository $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    /**
     * @throws NotFound
     */
    public function get(string $id): Question
    {
        return $this->questionRepository->mustFindOneById($id);
    }
}
