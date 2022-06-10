<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\GetPicProgramByLinkId;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramPic;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Query;

class GetProgramPicOfflineTextFromLinkIdController extends AbstractController
{
    private GetPicProgramByLinkId $getPicProgramByLinkId;

    public function __construct(GetPicProgramByLinkId $getPicProgramByLinkId)
    {
        $this->getPicProgramByLinkId = $getPicProgramByLinkId;
    }

    /**
     * @throws NotFound
     *
     * @Query
     */
    public function getProgramPicOfflineTextFromLinkId(string $id): ProgramPic
    {
        return $this->getPicProgramByLinkId->get($id);
    }
}
