<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\DocumentTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentCategoryRepository;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use Safe\DateTimeImmutable;

final class CreateDocument
{
    private DocumentRepository $documentRepository;
    private FileDescriptorRepository $fileDescriptorRepository;
    private UserRepository $userRepository;
    private DocumentCategoryRepository $documentCategoryRepository;

    public function __construct(FileDescriptorRepository $fileDescriptorRepository, DocumentRepository $documentRepository, UserRepository $userRepository, DocumentCategoryRepository $documentCategoryRepository)
    {
        $this->fileDescriptorRepository = $fileDescriptorRepository;
        $this->documentRepository = $documentRepository;
        $this->userRepository = $userRepository;
        $this->documentCategoryRepository = $documentCategoryRepository;
    }

    /**
     * @throws NotFound
     */
    public function create(
        string $name,
        string $description,
        string $tags,
        string $visibility,
        ?string $fileDescriptorId,
        string $authorId,
        string $categoryId,
        ?string $elaborationDate = null,
        ?string $type = null,
        ?string $articleLink = null,
        bool $toBeDisplayedInHomePage = false,
        bool $toSign = false,
        bool $hidden = false,
        ?string $livrableId = null
    ): Document {
        $author = $this->userRepository->mustFindOneById($authorId);
        $document = new Document($author, $name, $description, $tags, $visibility);
        $document->setType($type ?? DocumentTypeEnum::FILE);
        $document->setToBeSigned($toSign);

        if ($fileDescriptorId !== null) {
            $fileDescriptor = $this->fileDescriptorRepository->mustFindOneById($fileDescriptorId);
            $document->setFileDescriptor($fileDescriptor);
        }

        if ($type === DocumentTypeEnum::ARTICLE) {
            $document->setArticleLink($articleLink);
        }
        $document->setToBeDisplayedInHomePage($toBeDisplayedInHomePage);
        $elaborationDate = $elaborationDate !== null ? new DateTimeImmutable($elaborationDate) : null;
        $document->setElaborationDate($elaborationDate);
        $document->setHidden($hidden);
        $document->setCategory($this->documentCategoryRepository->mustFindOneById($categoryId));
        $document->setLivrable(! empty($livrableId) ? $this->documentRepository->mustFindOneById($livrableId) : null);
        $this->documentRepository->save($document);

        return $document;
    }
}
