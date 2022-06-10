<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Route;

use App\Application\Event\ExportEvents;
use App\Infrastructure\Annotations\Admin;
use App\Infrastructure\Config\EnvVarHelper;
use Safe\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;
use function Safe\file_get_contents;
use function Safe\json_decode;
use function Safe\unlink;

final class ExportEventsController extends AbstractController
{
    private ExportEvents $exportEvents;
    private EnvVarHelper $envVarHelper;

    public function __construct(ExportEvents $exportEvents, EnvVarHelper $envVarHelper)
    {
        $this->exportEvents = $exportEvents;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @Admin
     * @Route("/export/events")
     */
    public function exportEvents(Request $request): Response
    {
        $filepath = $this->exportEvents->export(
            $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH),
            json_decode($request->query->get('statusLabels'), true),
            $request->query->get('search'),
            $request->query->get('status'),
            $request->query->get('organizer'),
            $request->query->get('user'),
            $request->query->get('startDate'),
            $request->query->get('endDate'),
            $request->query->get('sortColumn'),
            $request->query->get('sortDirection'),
        );

        $filename = 'evenements_' . (new DateTimeImmutable())->format('d\_m\_Y') . '.xlsx';

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
