<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Model\FileDescriptor;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\SignRepository;
use function is_string;
use function Safe\base64_decode;
use function Safe\file_put_contents;
use function Safe\parse_url;

final class UpdateFileDocument
{
    private DocumentRepository $documentRepository;
    private SignRepository $signRepository;

    public function __construct(DocumentRepository $documentRepository, SignRepository $signRepository)
    {
        $this->documentRepository = $documentRepository;
        $this->signRepository = $signRepository;
    }

    /**
     * @throws NotFound
     */
    public function updateFileDocument(
        string $id,
        string $idFile,
        string $rootPath
    ): Document {
        $document = $this->documentRepository->mustFindOneByProcedureId($id);

        if ($document->getFileDescriptor() === null) {
            throw new NotFound(FileDescriptor::class, ['Document id' => $document->getId()]);
        }

        $upstream = parse_url($document->getFileDescriptor()->getUpstream());
        $filepath = $rootPath . $upstream['path'];

        $file = $this->signRepository->downloadSignedFile($idFile, null);
        if (is_string($file)) {
            $file = base64_decode($file);
        }
        file_put_contents($filepath, $file);

        $this->documentRepository->saveNoLog($document);

        return $document;
    }
}
