<?php
/**
 * This file has been automatically generated by TDBM.
 *
 * DO NOT edit this file, as it might be overwritten.
 * If you need to perform changes, edit the Todo class instead!
 */

declare(strict_types=1);

namespace App\Domain\Model\Generated;

use App\Domain\Model\Program;
use App\Domain\Model\User;
use TheCodingMachine\TDBM\AbstractTDBMObject;
use TheCodingMachine\TDBM\ResultIterator;
use TheCodingMachine\TDBM\AlterableResultIterator;
use Ramsey\Uuid\Uuid;
use JsonSerializable;
use TheCodingMachine\TDBM\Schema\ForeignKeys;
use TheCodingMachine\GraphQLite\Annotations\Field as GraphqlField;

/**
 * The AbstractTodo class maps the 'todos' table in database.
 */
abstract class AbstractTodo extends \TheCodingMachine\TDBM\AbstractTDBMObject implements JsonSerializable
{

    /**
     * @var \TheCodingMachine\TDBM\Schema\ForeignKeys
     */
    private static $foreignKeys = null;

    /**
     * The constructor takes all compulsory arguments.
     *
     * @param \App\Domain\Model\Program $program
     * @param string $label
     */
    public function __construct(\App\Domain\Model\Program $program, string $label)
    {
        parent::__construct();
        $this->setProgram($program);
        $this->setLabel($label);
        $this->setId(Uuid::uuid1()->toString());
        $this->setDone(false);
    }

    /**
     * The getter for the "id" column.
     *
     * @return string
     * @GraphqlField (outputType = "ID")
     */
    public function getId() : string
    {
        return $this->get('id', 'todos');
    }

    /**
     * The setter for the "id" column.
     *
     * @param string $id
     */
    public function setId(string $id) : void
    {
        $this->set('id', $id, 'todos');
    }

    /**
     * Returns the Program object bound to this object via the program_id column.
     *
     * @GraphqlField
     */
    public function getProgram() : \App\Domain\Model\Program
    {
        return $this->getRef('from__program_id__to__table__programs__columns__id', 'todos');
    }

    /**
     * The setter for the Program object bound to this object via the program_id
     * column.
     */
    public function setProgram(\App\Domain\Model\Program $object) : void
    {
        $this->setRef('from__program_id__to__table__programs__columns__id', $object, 'todos');
    }

    /**
     * Returns the User object bound to this object via the created_by column.
     *
     * @GraphqlField
     */
    public function getCreatedBy() : ?\App\Domain\Model\User
    {
        return $this->getRef('from__created_by__to__table__users__columns__id', 'todos');
    }

    /**
     * The setter for the User object bound to this object via the created_by column.
     */
    public function setCreatedBy(?\App\Domain\Model\User $object) : void
    {
        $this->setRef('from__created_by__to__table__users__columns__id', $object, 'todos');
    }

    /**
     * Returns the User object bound to this object via the updated_by column.
     *
     * @GraphqlField
     */
    public function getUpdatedBy() : ?\App\Domain\Model\User
    {
        return $this->getRef('from__updated_by__to__table__users__columns__id', 'todos');
    }

    /**
     * The setter for the User object bound to this object via the updated_by column.
     */
    public function setUpdatedBy(?\App\Domain\Model\User $object) : void
    {
        $this->setRef('from__updated_by__to__table__users__columns__id', $object, 'todos');
    }

    /**
     * Returns the User object bound to this object via the user_id column.
     *
     * @GraphqlField
     */
    public function getUser() : ?\App\Domain\Model\User
    {
        return $this->getRef('from__user_id__to__table__users__columns__id', 'todos');
    }

    /**
     * The setter for the User object bound to this object via the user_id column.
     */
    public function setUser(?\App\Domain\Model\User $object) : void
    {
        $this->setRef('from__user_id__to__table__users__columns__id', $object, 'todos');
    }

    /**
     * The getter for the "label" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getLabel() : string
    {
        return $this->get('label', 'todos');
    }

    /**
     * The setter for the "label" column.
     *
     * @param string $label
     */
    public function setLabel(string $label) : void
    {
        $this->set('label', $label, 'todos');
    }

    /**
     * The getter for the "done" column.
     *
     * @return bool
     * @GraphqlField
     */
    public function getDone() : bool
    {
        return $this->get('done', 'todos');
    }

    /**
     * The setter for the "done" column.
     *
     * @param bool $done
     */
    public function setDone(bool $done) : void
    {
        $this->set('done', $done, 'todos');
    }

    /**
     * The getter for the "created_at" column.
     *
     * @return \DateTimeImmutable|null
     * @GraphqlField
     */
    public function getCreatedAt() : ?\DateTimeImmutable
    {
        return $this->get('created_at', 'todos');
    }

    /**
     * The setter for the "created_at" column.
     *
     * @param \DateTimeImmutable|null $created_at
     */
    public function setCreatedAt(?\DateTimeImmutable $created_at) : void
    {
        $this->set('created_at', $created_at, 'todos');
    }

    /**
     * The getter for the "updated_at" column.
     *
     * @return \DateTimeImmutable|null
     * @GraphqlField
     */
    public function getUpdatedAt() : ?\DateTimeImmutable
    {
        return $this->get('updated_at', 'todos');
    }

    /**
     * The setter for the "updated_at" column.
     *
     * @param \DateTimeImmutable|null $updated_at
     */
    public function setUpdatedAt(?\DateTimeImmutable $updated_at) : void
    {
        $this->set('updated_at', $updated_at, 'todos');
    }

    /**
     * Internal method used to retrieve the list of foreign keys attached to this bean.
     */
    protected static function getForeignKeys(string $tableName) : \TheCodingMachine\TDBM\Schema\ForeignKeys
    {
        if ($tableName === 'todos') {
            if (self::$foreignKeys === null) {
                self::$foreignKeys = new ForeignKeys([
                    'from__created_by__to__table__users__columns__id' => [
                        'foreignTable' => 'users',
                        'localColumns' => [
                            'created_by'
                        ],
                        'foreignColumns' => [
                            'id'
                        ]
                    ],
                    'from__program_id__to__table__programs__columns__id' => [
                        'foreignTable' => 'programs',
                        'localColumns' => [
                            'program_id'
                        ],
                        'foreignColumns' => [
                            'id'
                        ]
                    ],
                    'from__updated_by__to__table__users__columns__id' => [
                        'foreignTable' => 'users',
                        'localColumns' => [
                            'updated_by'
                        ],
                        'foreignColumns' => [
                            'id'
                        ]
                    ],
                    'from__user_id__to__table__users__columns__id' => [
                        'foreignTable' => 'users',
                        'localColumns' => [
                            'user_id'
                        ],
                        'foreignColumns' => [
                            'id'
                        ]
                    ]
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
        if ($stopRecursion) {
            $array['program'] = ['id' => $this->getProgram()->getId()];
        } else {
            $array['program'] = $this->getProgram()->jsonSerialize(true);
        }
        if ($stopRecursion) {
            $array['createdBy'] = ($object = $this->getCreatedBy()) ? ['id' => $object->getId()] : null;
        } else {
            $array['createdBy'] = ($object = $this->getCreatedBy()) ? $object->jsonSerialize(true) : null;
        }
        if ($stopRecursion) {
            $array['updatedBy'] = ($object = $this->getUpdatedBy()) ? ['id' => $object->getId()] : null;
        } else {
            $array['updatedBy'] = ($object = $this->getUpdatedBy()) ? $object->jsonSerialize(true) : null;
        }
        if ($stopRecursion) {
            $array['user'] = ($object = $this->getUser()) ? ['id' => $object->getId()] : null;
        } else {
            $array['user'] = ($object = $this->getUser()) ? $object->jsonSerialize(true) : null;
        }
        $array['label'] = $this->getLabel();
        $array['done'] = $this->getDone();
        $array['createdAt'] = ($date = $this->getCreatedAt()) ? $date->format('c') : null;
        $array['updatedAt'] = ($date = $this->getUpdatedAt()) ? $date->format('c') : null;
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
        return [ 'todos' ];
    }

    /**
     * Method called when the bean is removed from database.
     */
    public function onDelete() : void
    {
        parent::onDelete();
        $this->setRef('from__program_id__to__table__programs__columns__id', null, 'todos');
        $this->setRef('from__created_by__to__table__users__columns__id', null, 'todos');
        $this->setRef('from__updated_by__to__table__users__columns__id', null, 'todos');
        $this->setRef('from__user_id__to__table__users__columns__id', null, 'todos');
    }

    public function __clone()
    {
        parent::__clone();
        $this->setId(Uuid::uuid1()->toString());
    }
}