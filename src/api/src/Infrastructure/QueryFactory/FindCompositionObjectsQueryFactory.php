<?php

declare(strict_types=1);

namespace App\Infrastructure\QueryFactory;

use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Schema\Schema;
use Mouf\Database\SchemaAnalyzer\SchemaAnalyzer;
use TheCodingMachine\TDBM\OrderByAnalyzer;
use TheCodingMachine\TDBM\QueryFactory\FindObjectsFromSqlQueryFactory;
use TheCodingMachine\TDBM\TDBMService;
use TheCodingMachine\TDBM\UncheckedOrderBy;
use function array_map;
use function array_merge;
use function implode;

class FindCompositionObjectsQueryFactory extends FindObjectsFromSqlQueryFactory
{
    /** @var string[] */
    private array $tables;
    /** @var string[] */
    private array $additionalSelect;
    private string $sqlFrom;
    private string $sqlFilter;

    /**
     * @param string[] $tables
     * @param string|UncheckedOrderBy $orderBy
     * @param string[] $additionalSelect
     */
    public function __construct(array $tables, string $from, string $filterString, $orderBy, TDBMService $tdbmService, Schema $schema, OrderByAnalyzer $orderByAnalyzer, SchemaAnalyzer $schemaAnalyzer, Cache $cache, string $cachePrefix, array $additionalSelect = [])
    {
        parent::__construct($tables[0], $from, $filterString, $orderBy, $tdbmService, $schema, $orderByAnalyzer, $schemaAnalyzer, $cache, $cachePrefix);
        $this->tables = $tables;
        $this->additionalSelect = $additionalSelect;
        $this->sqlFrom = $from;
        $this->sqlFilter = $filterString;
    }

    protected function compute(): void
    {
        $mysqlPlatform = new MySqlPlatform();

        [$columnDescList, $columnsList, $orderString] = $this->getColumnsList($this->tables[0], $this->tables, $this->orderBy, false);

        $sql = 'SELECT DISTINCT ' . implode(', ', array_merge($columnsList, $this->additionalSelect)) . ' FROM ' . $this->sqlFrom;

        $pkColumnNames = [];
        foreach ($this->tables as $table) {
            $columnNames = $this->tdbmService->getPrimaryKeyColumns($table);
            $columnNames = array_map(static function ($pkColumn) use ($table, $mysqlPlatform) {
                return $mysqlPlatform->quoteIdentifier($table) . '.' . $mysqlPlatform->quoteIdentifier($pkColumn);
            }, $columnNames);

            $pkColumnNames = array_merge($pkColumnNames, $columnNames);
        }

        $countSql = 'SELECT COUNT(DISTINCT ' . implode(', ', $pkColumnNames) . ') FROM ' . $this->sqlFrom;

        if (! empty($this->sqlFilter)) {
            $sql .= ' WHERE ' . $this->sqlFilter;
            $countSql .= ' WHERE ' . $this->sqlFilter;
        }

        if (! empty($orderString)) {
            $sql .= ' ORDER BY ' . $orderString;
        }

        $this->magicSql = $sql;
        $this->magicSqlCount = $countSql;
        $this->columnDescList = $columnDescList;
    }
}
