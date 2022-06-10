<?php

declare(strict_types=1);

namespace App\Application\Faq;

use App\Domain\Repository\QuestionRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllQuestions
{
    private QuestionRepository $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    public function getAll(?string $search, ?string $theme, ?string $sortColumn = null, ?string $sortDirection = null): ResultIterator
    {
        return $this->questionRepository->findByFilters($search, $theme, $sortColumn, $sortDirection);
    }
}
