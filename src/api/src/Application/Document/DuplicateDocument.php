<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\DocumentTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Model\FileDescriptor;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use function file_exists;
use function hash;
use function is_writable;
use function mt_rand;
use function pathinfo;
use function Safe\copy;
use function Safe\parse_url;

final class DuplicateDocument
{
    private DocumentRepository $documentRepository;
    private FileDescriptorRepository $fileDescriptorRepository;
    private UserRepository $userRepository;

    public function __construct(FileDescriptorRepository $fileDescriptorRepository, DocumentRepository $documentRepository, UserRepository $userRepository)
    {
        $this->fileDescriptorRepository = $fileDescriptorRepository;
        $this->documentRepository = $documentRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws NotFound
     */
    public function duplicate(string $documentId, string $rootPath): Document
    {
        $document = $this->documentRepository->mustFindOneById($documentId);
        $newDocument = new Document(
            $this->userRepository->getLoggedUser(),
            $document->getName(),
            $document->getDescription(),
            $document->getTags(),
            $document->getVisibility(),
        );

        $newDocument->setType($document->getType() ?? DocumentTypeEnum::FILE);
        $fileDescriptor = $document->getFileDescriptor();
        if ($newDocument->getType() === DocumentTypeEnum::ARTICLE) {
            $newDocument->setArticleLink($document->getArticleLink());
        } elseif ($fileDescriptor !== null) {
            $upstream = $fileDescriptor->getUpstream();
            $urlinfo = parse_url($upstream);

            $path = $urlinfo && isset($urlinfo['path']) ? $urlinfo['path'] : '';
            $fileinfo = pathinfo($path);

            $newUpstream = $fileinfo['dirname'] . '/' . hash('sha256', (string) mt_rand()) . (isset($fileinfo['extension']) ? '.' . $fileinfo['extension'] : '');
            $dest = $rootPath . $newUpstream;

            if (is_writable($rootPath) && file_exists($rootPath . $path)) {
                copy($rootPath . $path, $dest);
            }

            $newFileDescriptor = new FileDescriptor($fileDescriptor->getName(), $fileDescriptor->getSize(), 'file://' . $newUpstream);
            $this->fileDescriptorRepository->save($newFileDescriptor);

            $newDocument->setFileDescriptor($newFileDescriptor);
        }
        $newDocument->setToBeDisplayedInHomePage($document->getToBeDisplayedInHomePage());
        $newDocument->setElaborationDate($document->getElaborationDate());
        $newDocument->setToBeSigned($document->getToBeSigned());
        $this->documentRepository->save($newDocument);

        return $newDocument;
    }
}
