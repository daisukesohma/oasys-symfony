<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramPic;

interface ProgramPicRepository
{
    public function save(ProgramPic $programpic): void;

    public function saveNoLog(ProgramPic $programpic): void;

    public function delete(ProgramPic $programpic): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): ProgramPic;

    public function findOneById(string $id): ?ProgramPic;

    /**
     * @throws NotFound
     */
    public function mustFindOneByLinkId(string $linkId): ProgramPic;
}
