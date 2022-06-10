<?php
/**
 * This file has been automatically generated by TDBM.
 *
 * DO NOT edit this file, as it might be overwritten.
 * If you need to perform changes, edit the ProgramCoachingIndividual class
 * instead!
 */

declare(strict_types=1);

namespace App\Domain\Model\Generated;

use App\Domain\Model\Program;
use App\Domain\Model\ProgramModel;
use App\Domain\Model\User;
use App\Domain\Model\Company;
use TheCodingMachine\TDBM\ResultIterator;
use TheCodingMachine\TDBM\AlterableResultIterator;
use Ramsey\Uuid\Uuid;
use JsonSerializable;
use TheCodingMachine\TDBM\Schema\ForeignKeys;
use TheCodingMachine\GraphQLite\Annotations\Field as GraphqlField;

/**
 * The AbstractProgramCoachingIndividual class maps the
 * 'program_coaching_individuals' table in database.
 */
abstract class AbstractProgramCoachingIndividual extends Program implements JsonSerializable
{

    /**
     * @var \TheCodingMachine\TDBM\Schema\ForeignKeys
     */
    private static $foreignKeys = null;

    /**
     * The constructor takes all compulsory arguments.
     *
     * @param string $name
     * @param string $description
     * @param string $type
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $phone
     */
    public function __construct(string $name, string $description, string $type, string $firstName, string $lastName, string $email, string $phone)
    {
        parent::__construct($name, $description, $type);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setPhone($phone);
    }

    /**
     * The getter for the "first_name" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getFirstName() : string
    {
        return $this->get('first_name', 'program_coaching_individuals');
    }

    /**
     * The setter for the "first_name" column.
     *
     * @param string $first_name
     */
    public function setFirstName(string $first_name) : void
    {
        $this->set('first_name', $first_name, 'program_coaching_individuals');
    }

    /**
     * The getter for the "last_name" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getLastName() : string
    {
        return $this->get('last_name', 'program_coaching_individuals');
    }

    /**
     * The setter for the "last_name" column.
     *
     * @param string $last_name
     */
    public function setLastName(string $last_name) : void
    {
        $this->set('last_name', $last_name, 'program_coaching_individuals');
    }

    /**
     * The getter for the "email" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getEmail() : string
    {
        return $this->get('email', 'program_coaching_individuals');
    }

    /**
     * The setter for the "email" column.
     *
     * @param string $email
     */
    public function setEmail(string $email) : void
    {
        $this->set('email', $email, 'program_coaching_individuals');
    }

    /**
     * The getter for the "phone" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getPhone() : string
    {
        return $this->get('phone', 'program_coaching_individuals');
    }

    /**
     * The setter for the "phone" column.
     *
     * @param string $phone
     */
    public function setPhone(string $phone) : void
    {
        $this->set('phone', $phone, 'program_coaching_individuals');
    }

    /**
     * Internal method used to retrieve the list of foreign keys attached to this bean.
     */
    protected static function getForeignKeys(string $tableName) : \TheCodingMachine\TDBM\Schema\ForeignKeys
    {
        if ($tableName === 'program_coaching_individuals') {
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
        $array = parent::jsonSerialize($stopRecursion);
        $array['firstName'] = $this->getFirstName();
        $array['lastName'] = $this->getLastName();
        $array['email'] = $this->getEmail();
        $array['phone'] = $this->getPhone();
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
        $tables = parent::getUsedTables();
        $tables[] = 'program_coaching_individuals';

        return $tables;
    }

    public function __clone()
    {
        parent::__clone();
        $this->setId(Uuid::uuid1()->toString());
    }
}