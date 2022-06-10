<?php

declare(strict_types=1);

namespace App\Infrastructure\Iterator;

use App\Infrastructure\QueryFactory\FindCompositionObjectsQueryFactory;
use Closure;
use Mouf\Database\MagicQuery;
use Porpaginas\Page;
use Porpaginas\Result;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\TDBM\AbstractTDBMObject;
use TheCodingMachine\TDBM\TDBMService;
use function array_values;
use function iterator_to_array;

/**
 * @Type
 */
class CompositionResultIterator implements Result
{
    /** @var mixed[] $parameters */
    private array $parameters;
    private ?int $count = null;

    private FindCompositionObjectsQueryFactory $queryFactory;
    private TDBMService $tdbmService;
    private MagicQuery $magicQuery;
    private Closure $callback;

    /**
     * @param mixed[] $parameters
     */
    public function __construct(FindCompositionObjectsQueryFactory $queryFactory, array $parameters, TDBMService $tdbmService, MagicQuery $magicQuery, Closure $callback)
    {
        $this->queryFactory = $queryFactory;
        $this->tdbmService = $tdbmService;
        $this->magicQuery = $magicQuery;
        $this->parameters = $parameters;
        $this->callback = $callback;
    }

    /**
     * @param mixed $offset
     * @param mixed $limit
     *
     * @return PageCompositionResultIterator<object>
     */
    public function take($offset, $limit): Page
    {
        return new PageCompositionResultIterator($this->queryFactory, $this->parameters, $this->tdbmService, $this->magicQuery, $this->callback, $offset, $limit);
    }

    /**
     * @return InternalCompositionResultIterator<object>
     */
    public function getIterator(): InternalCompositionResultIterator
    {
        return new InternalCompositionResultIterator($this->queryFactory, $this->parameters, $this->tdbmService, $this->magicQuery, $this->callback);
    }

    public function count(): int
    {
        if ($this->count !== null) {
            return $this->count;
        }

        $query = $this->tdbmService->getConnection()->executeQuery($this->queryFactory->getMagicSqlCount(), $this->parameters);
        $this->count = (int) $query->fetchColumn(0);

        return $this->count;
    }

    /**
     * Casts the result set to a PHP array.
     *
     * @return AbstractTDBMObject[]
     */
    public function toArray(): array
    {
        return array_values(iterator_to_array($this->getIterator()));
    }
}
