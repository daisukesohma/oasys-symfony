<?php

declare(strict_types=1);

namespace App\Infrastructure\Dao;

use App\Infrastructure\Iterator\CompositionResultIterator;
use App\Infrastructure\QueryFactory\FindCompositionObjectsQueryFactory;
use Closure;
use TheCodingMachine\TDBM\UncheckedOrderBy;
use function array_map;
use function implode;

trait CompositionDao
{
    /**
     * @param string[] $tables
     * @param string[] $filters
     * @param mixed[] $parameters
     * @param string[] $additionalSelect
     */
    protected function findCompositionObjects(
        array $tables,
        string $from,
        array $filters,
        array $parameters,
        string $orderBy,
        Closure $closure,
        array $additionalSelect = []
    ): CompositionResultIterator {
        $queryFactory = new FindCompositionObjectsQueryFactory(
            $tables,
            $from,
            implode(' AND ', array_map(static fn(string $filter) => '(' . $filter . ')', $filters)),
            new UncheckedOrderBy($orderBy),
            $this->tdbmService,
            $this->tdbmSchemaAnalyzer->getSchema(),
            $this->orderByAnalyzer,
            $this->schemaAnalyzer,
            $this->cache,
            $this->cachePrefix,
            $additionalSelect
        );

        return new CompositionResultIterator(
            $queryFactory,
            $parameters,
            $this->tdbmService,
            $this->magicQuery,
            $closure,
        );
    }
}
