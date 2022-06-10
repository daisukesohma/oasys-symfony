<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Enum\RightEnum;
use App\Domain\Exception\InvalidRight;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\UserRepository;

final class DeleteDocument
{
    private DocumentRepository $documentRepository;
    private ProgramRepository $programRepository;
    private EventRepository $eventRepository;
    private UserRepository $userRepository;

    public function __construct(DocumentRepository $documentRepository, ProgramRepository $programRepository, EventRepository $eventRepository, UserRepository $userRepository)
    {
        $this->documentRepository = $documentRepository;
        $this->programRepository = $programRepository;
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws NotFound
     * @throws InvalidRight
     */
    public function delete(string $id): Document
    {
        $user = $this->userRepository->getLoggedUser();
        $document = $this->documentRepository->mustFindOneById($id);

        if (! $user->hasRight(RightEnum::DELETE_DOCUMENT_CODE)
            && $document->getAuthor()->getId() !== $user->getId()) {
            throw new InvalidRight();
        }

        $document->setDeleted(true);

        $this->documentRepository->save($document);

        foreach ($document->getPrograms() as $program) {
            $program->removeDocument($document);
            $this->programRepository->save($program);
        }

        foreach ($document->getEvents() as $event) {
            $event->removeDocument($document);
            $this->eventRepository->save($event);
        }

        return $document;
    }
}
