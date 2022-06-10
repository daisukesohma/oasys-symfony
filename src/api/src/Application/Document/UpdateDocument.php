<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\DocumentTypeEnum;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentCategoryRepository;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use DateTimeImmutable;

final class UpdateDocument
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
     * @throws InvalidStringValue
     */
    public function update(
        string $id,
        string $name,
        string $description,
        string $tags,
        string $visibility,
        ?string $fileDescriptorId,
        string $authorId,
        string $categoryId,
        ?DateTimeImmutable $elaborationDate = null,
        ?string $type = null,
        ?string $articleLink = null,
        bool $toBeDisplayedInHomePage = false,
        ?string $livrableId = null
    ): Document {
        $document = $this->documentRepository->mustFindOneById($id);

        if ($type === DocumentTypeEnum::ARTICLE) {
            $document->setArticleLink($articleLink);
        }

        if ($fileDescriptorId !== null) {
            $fileDescriptor = $this->fileDescriptorRepository->mustFindOneById($fileDescriptorId);
            if ($fileDescriptor !== $document->getFileDescriptor()) {
                $oldFile = $document->getFileDescriptor();
            }
            $document->setFileDescriptor($fileDescriptor);
        }

        $author = $this->userRepository->mustFindOneById($authorId);

        $document->setVisibility($visibility);
        $document->setName($name);
        $document->setElaborationDate($elaborationDate);
        $document->setDescription($description);
        $document->setAuthor($author);
        $document->setTags($tags);
        $document->setType($type ?? DocumentTypeEnum::FILE);
        if ($type === DocumentTypeEnum::ARTICLE) {
            $document->setArticleLink($articleLink);
        } elseif ($fileDescriptorId !== null) {
            $fileDescriptor = $this->fileDescriptorRepository->mustFindOneById($fileDescriptorId);
            $document->setFileDescriptor($fileDescriptor);
        }
        $document->setToBeDisplayedInHomePage($toBeDisplayedInHomePage);
        $document->setCategory($this->documentCategoryRepository->mustFindOneById($categoryId));
        $document->setLivrable(! empty($livrableId) ? $this->documentRepository->mustFindOneById($livrableId) : null);
        $this->documentRepository->save($document);

        if (isset($oldFile)) {
            $this->fileDescriptorRepository->delete($oldFile);
        }

        return $document;
    }
}
