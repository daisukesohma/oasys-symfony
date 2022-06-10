<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Route;

use App\Domain\Exception\NotFound;
use App\Domain\Model\FileDescriptor;
use App\Infrastructure\Config\EnvVarHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;
use function file_exists;
use function Safe\file_get_contents;

final class GetInfoPDFController extends AbstractController
{
    private EnvVarHelper $envVarHelper;

    public function __construct(EnvVarHelper $envVarHelper)
    {
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @throws NotFound
     *
     * @Route("/file/info_pdf")
     */
    public function serveImportModel(): Response
    {
        $filename = $this->envVarHelper->fetch(EnvVarHelper::INFO_PDF_NAME);
        $filepath = $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH) . '/public/' . $filename;

        if (! file_exists($filepath)) {
            throw new NotFound(FileDescriptor::class, ['filename' => $filename]);
        }

        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $content = file_get_contents($filepath);

        $response = new Response($content ?? '', 200, $headers);
        $response->setSharedMaxAge(3600 * 24 * 365);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }
}
