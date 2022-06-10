<?php

declare(strict_types=1);

namespace App\Infrastructure\Iterator;

use Closure;
use Countable;
use Doctrine\DBAL\Driver\ResultStatement;
use Iterator;
use Mouf\Database\MagicQuery;
use PDO;
use ReflectionClass;
use TheCodingMachine\TDBM\AbstractTDBMObject;
use TheCodingMachine\TDBM\QueryFactory\QueryFactory;
use TheCodingMachine\TDBM\TDBMInvalidArgumentException;
use TheCodingMachine\TDBM\TDBMService;
use TheCodingMachine\TDBM\Utils\DbalUtils;
use function assert;
use function call_user_func;
use function class_exists;
use function Safe\substr;
use function strpos;

/**
 * @implements Iterator<int, object>
 */
class InternalCompositionResultIterator implements Iterator, Countable
{
    /** @var mixed[] */
    private array $parameters;
    /** @var mixed[] */
    private array $reflectionCache = [];

    private ?int $count = null;
    private int $key = -1;
    /** @var mixed */
    private $current = null;

    protected ?int $limit = null;
    protected ?int $offset = null;

    private QueryFactory $queryFactory;
    private TDBMService $tdbmService;
    private MagicQuery $magicQuery;
    /** @var ResultStatement<mixed>  */
    private ResultStatement $statement;
    private Closure $callback;

    /**
     * @param mixed[] $parameters
     */
    public function __construct(QueryFactory $queryFactory, array $parameters, TDBMService $tdbmService, MagicQuery $magicQuery, Closure $callback, ?int $offset = null, ?int $limit = null)
    {
        $this->queryFactory = $queryFactory;
        $this->tdbmService = $tdbmService;
        $this->magicQuery = $magicQuery;
        $this->parameters = $parameters;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->callback = $callback;
    }

    private function getQuery(): string
    {
        $sql = $this->queryFactory->getMagicSql();
        if ($this->limit !== null && $this->offset !== null) {
            $sql = $this->tdbmService->getConnection()->getDatabasePlatform()->modifyLimitQuery($sql, $this->limit, $this->offset);
        }

        return $sql;
    }

    private function executeQuery(): void
    {
        $this->statement = $this->tdbmService->getConnection()->executeQuery($this->getQuery(), $this->parameters, DbalUtils::generateArrayTypes($this->parameters));
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
     * @return mixed
     */
    public function current()
    {
        return $this->current;
    }

    public function next(): void
    {
        if (! isset($this->statement)) {
            $this->executeQuery();
        }

        $row = $this->statement->fetch(PDO::FETCH_ASSOC);

        if (! $row) {
            $this->current = null;

            return;
        }

        $columnDescriptors = $this->queryFactory->getColumnDescriptors();
        $beansData = [];
        foreach ($row as $key => $value) {
            if (! isset($columnDescriptors[$key])) {
                continue;
            }

            $columnDescriptor = $columnDescriptors[$key];

            if ($columnDescriptor['tableGroup'] === null) {
                continue;
            }

            $value = $columnDescriptor['type']->convertToPHPValue($value, $this->tdbmService->getConnection()->getDatabasePlatform());

            $beansData[$columnDescriptor['tableGroup']][$columnDescriptor['table']][$columnDescriptor['column']] = $value;
        }

        $tuple = [];
        foreach ($beansData as $table => $beanData) {
            $table = (string) $table;
            if (strpos($table, '_``_') !== false) {
                $table = substr($table, 0, strpos($table, '_``_'));
            }

            $class = $this->tdbmService->getBeanClassName((string) $table);
            if (! class_exists($class)) {
                throw new TDBMInvalidArgumentException();
            }

            if (! isset($this->reflectionCache[$class])) {
                $this->reflectionCache[$class] = new ReflectionClass($class);
            }

            $bean = $this->reflectionCache[$class]->newInstanceWithoutConstructor();
            assert($bean instanceof AbstractTDBMObject);
            $bean->_constructFromData($beanData, $this->tdbmService);

            $tuple[] = $bean;
        }

        $this->current = call_user_func($this->callback, $tuple, $row);
    }

    public function key(): int
    {
        return $this->key;
    }

    public function valid(): bool
    {
        return $this->current !== null;
    }

    public function rewind(): void
    {
        $this->executeQuery();
        $this->key = -1;
        $this->next();
    }
}
