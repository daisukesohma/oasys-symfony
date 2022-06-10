<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Route;

use App\Application\Document\ExportDocuments;
use App\Infrastructure\Annotations\Admin;
use App\Infrastructure\Config\EnvVarHelper;
use Safe\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;
use function Safe\file_get_contents;
use function Safe\set_time_limit;
use function Safe\unlink;

final class ExportDocumentsController extends AbstractController
{
    private ExportDocuments $exportDocuments;
    private EnvVarHelper $envVarHelper;

    public function __construct(ExportDocuments $exportDocuments, EnvVarHelper $envVarHelper)
    {
        $this->exportDocuments = $exportDocuments;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @Admin
     * @Route("/export/documents")
     */
    public function exportDocuments(Request $request): Response
    {
        set_time_limit(0);

        $filepath = $this->exportDocuments->export(
            $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH),
            $request->query->get('search'),
            $request->query->get('tagSearch'),
            $request->query->get('visibility'),
            $request->query->get('type'),
            $request->query->get('categoryId'),
            $request->query->get('signaturePending') === 'true',
            $request->query->get('signedByCoach') === 'true',
            $request->query->get('signedByCandidate') === 'true',
            $request->query->get('livrableId'),
            $request->query->get('programId'),
            $request->query->get('authorId'),
            $request->query->get('eventId'),
            $request->query->get('createdAt'),
        );

        $filename = 'documents_' . (new DateTimeImmutable())->format('d\_m\_Y') . '.xlsx';

        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $content = file_get_contents($filepath);
        unlink($filepath);

        $response = new Response($content ?? '', 200, $headers);
        $response->setSharedMaxAge(3600 * 24 * 365);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }
}
