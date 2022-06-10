<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Repository\DocumentRepository;
use App\Domain\Util\Time;
use OneSheet\Writer;

class ExportDocuments
{
    use Time;

    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * @return string[]
     */
    protected function getHeaderValues(): array
    {
        return [
            'Nom',
            'Date de création',
            'Auteur',
            'Catégorie',
            'Prestation',
            'Evénement',
            'Description',
            'Lien',
            'Nom du fichier',
            'A signer',
            'Signé par le coach',
            'Signé par le candidat',
            'Signé par le N+1',
            'Tags',
        ];
    }

    public function export(
        string $rootPath,
        ?string $search,
        ?string $tagSearch,
        ?string $visibility,
        ?string $type,
        ?string $categoryId,
        ?bool $signaturePending,
        ?bool $signedByCoach,
        ?bool $signedByCandidate,
        ?string $livrableId,
        ?string $programId,
        ?string $authorId,
        ?string $eventId,
        ?string $createdAt
    ): string {
        $export = [$this->getHeaderValues()];
        $export = [
            ...$export,
            ...$this->documentRepository->findByFiltersForExport(
                $search,
                $tagSearch,
                $visibility,
                null,
                null,
                null,
                null,
                $type,
                null,
                null,
                $categoryId,
                $signaturePending,
                $signedByCoach,
                $signedByCandidate,
                $livrableId,
                $programId,
                $authorId,
                $eventId,
                $createdAt
            ),
        ];

        $path = $rootPath . 'public/files/export-document.xlsx';

        $onesheet = new Writer();
        $onesheet->addRows($export);
        $onesheet->writeToFile($path);

        return $path;
    }
}
