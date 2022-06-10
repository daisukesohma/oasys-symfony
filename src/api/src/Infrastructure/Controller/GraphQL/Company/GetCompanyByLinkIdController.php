<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Company;

use App\Application\Company\GetCompanyByLinkId;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetCompanyByLinkIdController extends AbstractController
{
    private GetCompanyByLinkId $getCompanyByLinkId;

    public function __construct(GetCompanyByLinkId $getCompanyByLinkId)
    {
        $this->getCompanyByLinkId = $getCompanyByLinkId;
    }

    /**
     * @throws NotFound
     *
     * @Query
     */
    public function getCompanyByLinkId(string $linkId): ?Company
    {
        return $this->getCompanyByLinkId->get($linkId);
    }
}
