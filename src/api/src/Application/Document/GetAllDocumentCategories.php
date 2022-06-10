<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Model\DocumentCategory;
use App\Domain\Repository\DocumentCategoryRepository;
use App\Domain\Repository\UserRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllDocumentCategories
{
    private DocumentCategoryRepository $documentCategoryRepository;
    private UserRepository $userRepository;

    public function __construct(DocumentCategoryRepository $documentCategoryRepository, UserRepository $userRepository)
    {
        $this->documentCategoryRepository = $documentCategoryRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return ResultIterator|DocumentCategory[]
     */
    public function getAll(): ResultIterator
    {
        return $this->documentCategoryRepository->getAll($this->userRepository->getLoggedUser());
    }
}
