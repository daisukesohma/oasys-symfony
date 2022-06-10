<?php

declare(strict_types=1);

namespace App\Infrastructure\Iterator;

use Closure;
use Mouf\Database\MagicQuery;
use Porpaginas\Page;
use TheCodingMachine\TDBM\AbstractTDBMObject;
use TheCodingMachine\TDBM\QueryFactory\QueryFactory;
use TheCodingMachine\TDBM\TDBMService;
use function array_values;
use function ceil;
use function iterator_to_array;

class PageCompositionResultIterator implements Page
{
    /** @var InternalCompositionResultIterator|object[] */
    private InternalCompositionResultIterator $iterator;
    private int $offset;
    private int $limit;

    /**
     * @param string[] $parameters
     */
    public function __construct(QueryFactory $queryFactory, array $parameters, TDBMService $tdbmService, MagicQuery $magicQuery, Closure $callback, int $offset, int $limit)
    {
        $this->iterator = new InternalCompositionResultIterator($queryFactory, $parameters, $tdbmService, $magicQuery, $callback, $offset, $limit);
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getCurrentOffset(): int
    {
        return $this->offset;
    }

    public function getCurrentPage(): int
    {
        return (int) ceil(($this->offset / $this->limit) + 1);
    }

    public function getCurrentLimit(): int
    {
        return $this->limit;
    }

    public function totalCount(): int
    {
        return $this->count();
    }

    /**
     * @return InternalCompositionResultIterator|object[]
     */
    public function getIterator(): InternalCompositionResultIterator
    {
        return $this->iterator;
    }

    public function count(): int
    {
        return $this->iterator->count();
    }

    /**
     * @return AbstractTDBMObject[]
     */
    public function toArray(): array
    {
        return array_values(iterator_to_array($this->getIterator()));
    }
}
