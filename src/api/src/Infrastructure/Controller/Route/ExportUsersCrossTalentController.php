<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Route;

use App\Application\User\ExportUsersCrossTalent;
use App\Infrastructure\Annotations\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;

final class ExportUsersCrossTalentController extends AbstractController
{
    private ExportUsersCrossTalent $exportUsersCrossTalent;

    public function __construct(ExportUsersCrossTalent $exportUsersCrossTalent)
    {
        $this->exportUsersCrossTalent = $exportUsersCrossTalent;
    }

    /**
     * @Admin
     * @Route("/export/users-cross-talent")
     */
    public function exportUsersCrossTalent(Request $request): Response
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="export_users.csv"',
        ];

        $content = $this->exportUsersCrossTalent->export(
            $request->query->get('search'),
            $request->query->get('companyName'),
            $request->query->get('types'),
            $request->query->get('roleId'),
            $request->query->get('companyId'),
            $request->query->get('programId'),
            $request->query->get('coachId'),
            $request->query->get('sortColumn'),
            $request->query->get('sortDirection'),
        );

        $response = new Response($content ?? '', 200, $headers);
        $response->setSharedMaxAge(3600 * 24 * 365);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }
}
