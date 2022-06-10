<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use App\Domain\Repository\ProgramPicRepository;

final class GetCompanyByLinkId
{
    private ProgramPicRepository $programRepository;

    public function __construct(ProgramPicRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    /**
     * @throws NotFound
     */
    public function get(string $id): ?Company
    {
        return $this->programRepository->mustFindOneByLinkId($id)->getCompany();
    }
}
