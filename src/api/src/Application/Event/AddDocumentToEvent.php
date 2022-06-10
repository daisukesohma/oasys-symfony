<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\Document\AddDocumentNotifier;
use App\Application\Document\DuplicateDocument;
use App\Application\Document\SendDocumentToSignFromEvent;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\YouSignError;
use App\Domain\Model\Event;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\EventRepository;

final class AddDocumentToEvent
{
    private DuplicateDocument $duplicateDocument;
    private EventRepository $eventRepository;
    private AddDocumentNotifier $addDocumentNotifier;
    private SendDocumentToSignFromEvent $sendDocumentToSignFromEvent;
    private DocumentRepository $documentRepository;

    public function __construct(
        DuplicateDocument $duplicateDocument,
        EventRepository $eventRepository,
        AddDocumentNotifier $addDocumentNotifier,
        SendDocumentToSignFromEvent $sendDocumentToSignFromEvent,
        DocumentRepository $documentRepository
    ) {
        $this->duplicateDocument = $duplicateDocument;
        $this->eventRepository = $eventRepository;
        $this->addDocumentNotifier = $addDocumentNotifier;
        $this->documentRepository = $documentRepository;
        $this->sendDocumentToSignFromEvent = $sendDocumentToSignFromEvent;
    }

    /**
     * @throws NotFound
     * @throws YouSignError
     */
    public function add(string $eventId, string $documentId, string $rootPath, string $webHookYouSignUrl): Event
    {
        $document = $this->duplicateDocument->duplicate($documentId, $rootPath);
        $event = $this->eventRepository->mustFindOneById($eventId);

        $this->sendDocumentToSignFromEvent->sendDocumentToSignFromEvent($event, $document, $webHookYouSignUrl, $rootPath);

        $document->setHidden(true);

        $this->documentRepository->save($document);
        $event->addDocument($document);
        $this->eventRepository->save($event);

        if ($document->getToBeSigned()) {
            return $event;
        }

        foreach ($event->getUsers() as $user) {
            $this->addDocumentNotifier->notify($document, $user);
        }

        return $event;
    }
}
