<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\GetProgramsForAutocomplete;
use App\Domain\Model\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetProgramsForAutocompleteController extends AbstractController
{
    private GetProgramsForAutocomplete $getProgramsForAutocomplete;

    public function __construct(GetProgramsForAutocomplete $getProgramsForAutocomplete)
    {
        $this->getProgramsForAutocomplete = $getProgramsForAutocomplete;
    }

    /**
     * @return ResultIterator|Program[]
     *
     * @Query
     * @Logged
     */
    public function getProgramsForAutocomplete(?string $search = null): ResultIterator
    {
        /** @var ResultIterator|Program[] $result */
        $result = $this->getProgramsForAutocomplete->get($search);

        return $result;
    }
}
