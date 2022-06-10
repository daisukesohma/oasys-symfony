<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Route;

use App\Application\Event\ExportEventsCrossTalent;
use App\Infrastructure\Annotations\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;

final class ExportEventsCrossTalentController extends AbstractController
{
    private ExportEventsCrossTalent $exportEventsCrossTalent;

    public function __construct(ExportEventsCrossTalent $exportEventsCrossTalent)
    {
        $this->exportEventsCrossTalent = $exportEventsCrossTalent;
    }

    /**
     * @Admin
     * @Route("/export/events-cross-talent")
     */
    public function exportEventsCrossTalent(Request $request): Response
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="export_events.csv"',
        ];

        $content = $this->exportEventsCrossTalent->export(
            $request->query->get('search'),
            $request->query->get('status'),
            $request->query->get('organizer'),
            $request->query->get('user'),
            $request->query->get('startDate'),
            $request->query->get('endDate'),
            $request->query->get('sortColumn'),
            $request->query->get('sortDirection'),
        );

        $response = new Response($content ?? '', 200, $headers);
        $response->setSharedMaxAge(3600 * 24 * 365);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }
}
