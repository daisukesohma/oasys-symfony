<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\DocumentEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;

final class CreateDocumentFromEvent
{
    private DocumentRepository $documentRepository;
    private FileDescriptorRepository $fileDescriptorRepository;
    private UserRepository $userRepository;
    private SendDocumentToSignFromEvent $sendDocumentToSignFromEvent;
    private CreateDocument $createDocument;
    private EventRepository $eventRepository;

    public function __construct(
        FileDescriptorRepository $fileDescriptorRepository,
        DocumentRepository $documentRepository,
        UserRepository $userRepository,
        SendDocumentToSignFromEvent $sendDocumentToSignFromEvent,
        EventRepository $eventRepository,
        CreateDocument $createDocument
    ) {
        $this->fileDescriptorRepository = $fileDescriptorRepository;
        $this->documentRepository = $documentRepository;
        $this->createDocument = $createDocument;
        $this->userRepository = $userRepository;
        $this->sendDocumentToSignFromEvent = $sendDocumentToSignFromEvent;
        $this->eventRepository = $eventRepository;
    }

    /**
     * @throws NotFound
     * @throws YouSignError
     */
    public function create(
        string $name,
        string $description,
        string $tags,
        ?string $fileDescriptorId,
        string $authorId,
        string $categoryId,
        string $eventId,
        bool $toSign,
        string $webHookYouSignUrl,
        string $rootPath,
        ?string $elaborationDate = null,
        bool $hidden = false,
        ?string $type = null,
        ?string $articleLink = null,
        bool $toBeDisplayedInHomePage = false,
        ?string $livrableId = null
    ): Document {
        $event = $this->eventRepository->mustFindOneById($eventId);
        $document = $this->createDocument->create(
            $name,
            $description,
            $tags,
            DocumentEnum::PROTECTED_CODE,
            $fileDescriptorId,
            $authorId,
            $categoryId,
            $elaborationDate,
            $type,
            $articleLink,
            $toBeDisplayedInHomePage,
            $toSign,
            $hidden,
            $livrableId
        );

        $this->sendDocumentToSignFromEvent->sendDocumentToSignFromEvent($event, $document, $webHookYouSignUrl, $rootPath);

        $this->documentRepository->save($document);
        $event->addDocument($document);
        $this->eventRepository->save($event);

        return $document;
    }
}
