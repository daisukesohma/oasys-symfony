<?php

declare(strict_types=1);

namespace App\Application\Faq;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Question;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\QuestionRepository;

final class UpdateQuestion
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
     * @throws InvalidStringValue
     */
    public function update(string $id, string $question, string $response, string $theme, string $programId): Question
    {
        $program = $this->programRepository->mustFindOneById($programId);
        $questionObject = $this->questionRepository->mustFindOneById($id);

        $questionObject->setQuestion($question);
        $questionObject->setResponse($response);
        $questionObject->setTheme($theme);
        $questionObject->setProgram($program);

        $this->questionRepository->save($questionObject);

        return $questionObject;
    }
}
