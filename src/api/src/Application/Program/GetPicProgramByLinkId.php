<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramPic;
use App\Domain\Repository\ProgramPicRepository;

final class GetPicProgramByLinkId
{
    private ProgramPicRepository $programRepository;

    public function __construct(ProgramPicRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    /**
     * @throws NotFound
     */
    public function get(string $id): ProgramPic
    {
        return $this->programRepository->mustFindOneByLinkId($id);
    }
}
