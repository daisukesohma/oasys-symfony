<?php
/**
 * This file has been automatically generated by TDBM.
 *
 * DO NOT edit this file, as it might be overwritten.
 * If you need to perform changes, edit the VilleFrance class instead!
 */

declare(strict_types=1);

namespace App\Domain\Model\Generated;

use TheCodingMachine\TDBM\AbstractTDBMObject;
use TheCodingMachine\TDBM\ResultIterator;
use TheCodingMachine\TDBM\AlterableResultIterator;
use Ramsey\Uuid\Uuid;
use JsonSerializable;
use TheCodingMachine\TDBM\Schema\ForeignKeys;
use TheCodingMachine\GraphQLite\Annotations\Field as GraphqlField;

/**
 * The AbstractVilleFrance class maps the 'villes_france' table in database.
 */
abstract class AbstractVilleFrance extends \TheCodingMachine\TDBM\AbstractTDBMObject implements JsonSerializable
{

    /**
     * @var \TheCodingMachine\TDBM\Schema\ForeignKeys
     */
    private static $foreignKeys = null;

    /**
     * The constructor takes all compulsory arguments.
     *
     * @param string $codePostal
     * @param string $departmentNumber
     * @param string $departmentName
     * @param string $commonName
     * @param string $regionName
     */
    public function __construct(string $codePostal, string $departmentNumber, string $departmentName, string $commonName, string $regionName)
    {
        parent::__construct();
        $this->setCodePostal($codePostal);
        $this->setDepartmentNumber($departmentNumber);
        $this->setDepartmentName($departmentName);
        $this->setCommonName($commonName);
        $this->setRegionName($regionName);
        $this->setId(Uuid::uuid1()->toString());
    }

    /**
     * The getter for the "id" column.
     *
     * @return string
     * @GraphqlField (outputType = "ID")
     */
    public function getId() : string
    {
        return $this->get('id', 'villes_france');
    }

    /**
     * The setter for the "id" column.
     *
     * @param string $id
     */
    public function setId(string $id) : void
    {
        $this->set('id', $id, 'villes_france');
    }

    /**
     * The getter for the "code_postal" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getCodePostal() : string
    {
        return $this->get('code_postal', 'villes_france');
    }

    /**
     * The setter for the "code_postal" column.
     *
     * @param string $code_postal
     */
    public function setCodePostal(string $code_postal) : void
    {
        $this->set('code_postal', $code_postal, 'villes_france');
    }

    /**
     * The getter for the "department_number" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getDepartmentNumber() : string
    {
        return $this->get('department_number', 'villes_france');
    }

    /**
     * The setter for the "department_number" column.
     *
     * @param string $department_number
     */
    public function setDepartmentNumber(string $department_number) : void
    {
        $this->set('department_number', $department_number, 'villes_france');
    }

    /**
     * The getter for the "department_name" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getDepartmentName() : string
    {
        return $this->get('department_name', 'villes_france');
    }

    /**
     * The setter for the "department_name" column.
     *
     * @param string $department_name
     */
    public function setDepartmentName(string $department_name) : void
    {
        $this->set('department_name', $department_name, 'villes_france');
    }

    /**
     * The getter for the "common_name" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getCommonName() : string
    {
        return $this->get('common_name', 'villes_france');
    }

    /**
     * The setter for the "common_name" column.
     *
     * @param string $common_name
     */
    public function setCommonName(string $common_name) : void
    {
        $this->set('common_name', $common_name, 'villes_france');
    }

    /**
     * The getter for the "region_name" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getRegionName() : string
    {
        return $this->get('region_name', 'villes_france');
    }

    /**
     * The setter for the "region_name" column.
     *
     * @param string $region_name
     */
    public function setRegionName(string $region_name) : void
    {
        $this->set('region_name', $region_name, 'villes_france');
    }

    /**
     * Internal method used to retrieve the list of foreign keys attached to this bean.
     */
    protected static function getForeignKeys(string $tableName) : \TheCodingMachine\TDBM\Schema\ForeignKeys
    {
        if ($tableName === 'villes_france') {
            if (self::$foreignKeys === null) {
                self::$foreignKeys = new ForeignKeys([

                ]);
            }
            return self::$foreignKeys;
        }
        return parent::getForeignKeys($tableName);
    }

    /**
     * Serializes the object for JSON encoding.
     *
     * @param bool $stopRecursion Parameter used internally by TDBM to stop embedded
     * objects from embedding other objects.
     * @return array
     */
    public function jsonSerialize(bool $stopRecursion = false)
    {
        $array = [];
        $array['id'] = $this->getId();
        $array['codePostal'] = $this->getCodePostal();
        $array['departmentNumber'] = $this->getDepartmentNumber();
        $array['departmentName'] = $this->getDepartmentName();
        $array['commonName'] = $this->getCommonName();
        $array['regionName'] = $this->getRegionName();
        return $array;
    }

    /**
     * Returns an array of used tables by this bean (from parent to child
     * relationship).
     *
     * @return string[]
     */
    public function getUsedTables() : array
    {
        return [ 'villes_france' ];
    }

    public function __clone()
    {
        parent::__clone();
        $this->setId(Uuid::uuid1()->toString());
    }
}
