<?php

declare(strict_types=1);

namespace App\Application\Faq;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Question;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\QuestionRepository;

final class CreateQuestion
{
    private QuestionRepository $questionRepository;
    private ProgramRepository $programRepository;

    public function __construct(QuestionRepository $questionRepository, ProgramRepository $programRepository)
    {
        $this->questionRepository = $questionRepository;
        $this->programRepository = $programRepository;
    }

    /**
     * @throws NotFound
     */
    public function create(string $question, string $response, string $theme, string $programId): Question
    {
        $program = $this->programRepository->mustFindOneById($programId);

        $question = new Question($question, $response, $theme);
        $question->setProgram($program);

        $this->questionRepository->save($question);

        return $question;
    }
}
