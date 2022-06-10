<?php
/**
 * This file has been automatically generated by TDBM.
 * DO NOT edit this file, as it might be overwritten.
 * If you need to perform changes, edit the ProgramCoachingIndividualDao class
 * instead!
 */

declare(strict_types=1);

namespace App\Infrastructure\Dao\Generated;

use App\Domain\Model\ProgramCoachingIndividual;
use TheCodingMachine\TDBM\TDBMService;
use TheCodingMachine\TDBM\ResultIterator;
use TheCodingMachine\TDBM\TDBMException;

/**
 * The AbstractProgramCoachingIndividualDao class will maintain the persistence of
 * ProgramCoachingIndividual class into the program_coaching_individuals table.
 */
abstract class AbstractProgramCoachingIndividualDao
{

    /**
     * @var \TheCodingMachine\TDBM\TDBMService
     */
    public $tdbmService = null;

    /**
     * The default sort column.
     *
     * @var string|null
     */
    public $defaultSort = null;

    /**
     * The default sort direction.
     *
     * @var string
     */
    public $defaultDirection = 'asc';

    /**
     * Sets the TDBM service used by this DAO.
     */
    public function __construct(\TheCodingMachine\TDBM\TDBMService $tdbmService)
    {
        $this->tdbmService = $tdbmService;
    }

    /**
     * Persist the ProgramCoachingIndividual instance.
     *
     * @param ProgramCoachingIndividual $obj The bean to save.
     */
    public function save(\App\Domain\Model\ProgramCoachingIndividual $obj) : void
    {
        $this->tdbmService->save($obj);
    }

    /**
     * Get all ProgramCoachingIndividual records.
     *
     * @return \App\Domain\Model\ProgramCoachingIndividual[]|\TheCodingMachine\TDBM\ResultIterator
     */
    public function findAll() : \TheCodingMachine\TDBM\ResultIterator
    {
        if ($this->defaultSort) {
            $orderBy = 'program_coaching_individuals.'.$this->defaultSort.' '.$this->defaultDirection;
        } else {
            $orderBy = null;
        }
        return $this->tdbmService->findObjects('program_coaching_individuals', null, [], $orderBy);
    }

    /**
     * Get ProgramCoachingIndividual specified by its ID (its primary key).
     *
     * If the primary key does not exist, an exception is thrown.
     *
     * @param string $id
     * @param bool $lazyLoading If set to true, the object will not be loaded right away. Instead, it will be loaded when you first try to access a method of the object.
     * @return \App\Domain\Model\ProgramCoachingIndividual
     * @throws \TheCodingMachine\TDBM\TDBMException
     * @TheCodingMachine\GraphQLite\Annotations\Factory
     * @TheCodingMachine\GraphQLite\Annotations\HideParameter (for="$lazyLoading")
     */
    public function getById(string $id, bool $lazyLoading = false) : \App\Domain\Model\ProgramCoachingIndividual
    {
        return $this->tdbmService->findObjectByPk('program_coaching_individuals', ['id' => $id], [], $lazyLoading);
    }

    /**
     * Get all ProgramCoachingIndividual records.
     *
     * @param \App\Domain\Model\ProgramCoachingIndividual $obj The object to delete
     * @param bool $cascade If true, it will delete all objects linked to $obj
     */
    public function delete(\App\Domain\Model\ProgramCoachingIndividual $obj, bool $cascade = false) : void
    {
        if ($cascade === true) {
            $this->tdbmService->deleteCascade($obj);
        } else {
            $this->tdbmService->delete($obj);
        }
    }

    /**
     * Get all ProgramCoachingIndividual records.
     *
     * @param mixed $filter The filter bag (see TDBMService::findObjects for complete description)
     * @param mixed[] $parameters The parameters associated with the filter
     * @param mixed $orderBy The order string
     * @param string[] $additionalTablesFetch A list of additional tables to fetch (for performance improvement)
     * @param int|null $mode Either TDBMService::MODE_ARRAY or TDBMService::MODE_CURSOR (for large datasets). Defaults to TDBMService::MODE_ARRAY.
     * @return \App\Domain\Model\ProgramCoachingIndividual[]|\TheCodingMachine\TDBM\ResultIterator
     */
    protected function find($filter = null, array $parameters = [], $orderBy = null, array $additionalTablesFetch = [], ?int $mode = null) : \TheCodingMachine\TDBM\ResultIterator
    {
        if ($this->defaultSort && $orderBy == null) {
            $orderBy = 'program_coaching_individuals.'.$this->defaultSort.' '.$this->defaultDirection;
        }
        return $this->tdbmService->findObjects('program_coaching_individuals', $filter, $parameters, $orderBy, $additionalTablesFetch, $mode);
    }

    /**
     * Get a list of ProgramCoachingIndividual specified by its filters.
     *
     * Unlike the `find` method that guesses the FROM part of the statement, here you can pass the $from part.
     *
     * You should not put an alias on the main table name. So your $from variable should look like:
     *
     *    "program_coaching_individuals JOIN ... ON ..."
     *
     * @param string $from The sql from statement
     * @param mixed $filter The filter bag (see TDBMService::findObjects for complete description)
     * @param mixed[] $parameters The parameters associated with the filter
     * @param mixed $orderBy The order string
     * @param string[] $additionalTablesFetch A list of additional tables to fetch (for performance improvement)
     * @param int|null $mode Either TDBMService::MODE_ARRAY or TDBMService::MODE_CURSOR (for large datasets). Defaults to TDBMService::MODE_ARRAY.
     * @return \App\Domain\Model\ProgramCoachingIndividual[]|\TheCodingMachine\TDBM\ResultIterator
     */
    protected function findFromSql(string $from, $filter = null, array $parameters = [], $orderBy = null, array $additionalTablesFetch = [], ?int $mode = null) : \TheCodingMachine\TDBM\ResultIterator
    {
        if ($this->defaultSort && $orderBy == null) {
            $orderBy = 'program_coaching_individuals.'.$this->defaultSort.' '.$this->defaultDirection;
        }
        return $this->tdbmService->findObjectsFromSql('program_coaching_individuals', $from, $filter, $parameters, $orderBy, $mode);
    }

    /**
     * Get a list of ProgramCoachingIndividual from a SQL query.
     *
     * Unlike the `find` and `findFromSql` methods, here you can pass the whole $sql query.
     *
     * You should not put an alias on the main table name, and select its columns using `*`. So the SELECT part of you $sql should look like:
     *
     *    "SELECT program_coaching_individuals .* FROM ..."
     *
     * @param string $sql The sql query
     * @param mixed[] $parameters The parameters associated with the query
     * @param string|null $countSql The sql query that provides total count of rows (automatically computed if not provided)
     * @param int|null $mode Either TDBMService::MODE_ARRAY or TDBMService::MODE_CURSOR (for large datasets). Defaults to TDBMService::MODE_ARRAY.
     * @return \App\Domain\Model\ProgramCoachingIndividual[]|\TheCodingMachine\TDBM\ResultIterator
     */
    protected function findFromRawSql(string $sql, array $parameters = [], ?string $countSql = null, ?int $mode = null) : \TheCodingMachine\TDBM\ResultIterator
    {
        return $this->tdbmService->findObjectsFromRawSql('program_coaching_individuals', $sql, $parameters, $mode, null, $countSql);
    }

    /**
     * Get a single ProgramCoachingIndividual specified by its filters.
     *
     * @param mixed $filter The filter bag (see TDBMService::findObjects for complete description)
     * @param mixed[] $parameters The parameters associated with the filter
     * @param string[] $additionalTablesFetch A list of additional tables to fetch (for performance improvement)
     * @return \App\Domain\Model\ProgramCoachingIndividual|null
     */
    protected function findOne($filter = null, array $parameters = [], array $additionalTablesFetch = []) : ?\App\Domain\Model\ProgramCoachingIndividual
    {
        return $this->tdbmService->findObject('program_coaching_individuals', $filter, $parameters, $additionalTablesFetch);
    }

    /**
     * Get a single ProgramCoachingIndividual specified by its filters.
     *
     * Unlike the `findOne` method that guesses the FROM part of the statement, here you can pass the $from part.
     *
     * You should not put an alias on the main table name. So your $from variable should look like:
     *
     *     "program_coaching_individuals JOIN ... ON ..."
     *
     * @param string $from The sql from statement
     * @param mixed $filter The filter bag (see TDBMService::findObjects for complete description)
     * @param mixed[] $parameters The parameters associated with the filter
     * @return \App\Domain\Model\ProgramCoachingIndividual|null
     */
    protected function findOneFromSql(string $from, $filter = null, array $parameters = []) : ?\App\Domain\Model\ProgramCoachingIndividual
    {
        return $this->tdbmService->findObjectFromSql('program_coaching_individuals', $from, $filter, $parameters);
    }

    /**
     * Sets the default column for default sorting.
     *
     * @param string $defaultSort
     */
    public function setDefaultSort(string $defaultSort) : void
    {
        $this->defaultSort = $defaultSort;
    }
}
