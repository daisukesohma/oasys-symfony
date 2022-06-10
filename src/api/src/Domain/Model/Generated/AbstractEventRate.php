<?php
/**
 * This file has been automatically generated by TDBM.
 *
 * DO NOT edit this file, as it might be overwritten.
 * If you need to perform changes, edit the EventRate class instead!
 */

declare(strict_types=1);

namespace App\Domain\Model\Generated;

use App\Domain\Model\Event;
use App\Domain\Model\User;
use TheCodingMachine\TDBM\AbstractTDBMObject;
use TheCodingMachine\TDBM\ResultIterator;
use TheCodingMachine\TDBM\AlterableResultIterator;
use Ramsey\Uuid\Uuid;
use JsonSerializable;
use TheCodingMachine\TDBM\Schema\ForeignKeys;
use TheCodingMachine\GraphQLite\Annotations\Field as GraphqlField;

/**
 * The AbstractEventRate class maps the 'events_rates' table in database.
 */
abstract class AbstractEventRate extends \TheCodingMachine\TDBM\AbstractTDBMObject implements JsonSerializable
{

    /**
     * @var \TheCodingMachine\TDBM\Schema\ForeignKeys
     */
    private static $foreignKeys = null;

    /**
     * The constructor takes all compulsory arguments.
     *
     * @param \App\Domain\Model\Event $event
     * @param \App\Domain\Model\User $user
     * @param int $starsNumber
     */
    public function __construct(\App\Domain\Model\Event $event, \App\Domain\Model\User $user, int $starsNumber)
    {
        parent::__construct();
        $this->setEvent($event);
        $this->setUser($user);
        $this->setStarsNumber($starsNumber);
    }

    /**
     * Returns the Event object bound to this object via the event_id column.
     *
     * @GraphqlField
     */
    public function getEvent() : \App\Domain\Model\Event
    {
        return $this->getRef('from__event_id__to__table__events__columns__id', 'events_rates');
    }

    /**
     * The setter for the Event object bound to this object via the event_id column.
     */
    public function setEvent(\App\Domain\Model\Event $object) : void
    {
        $this->setRef('from__event_id__to__table__events__columns__id', $object, 'events_rates');
    }

    /**
     * Returns the User object bound to this object via the user_id column.
     *
     * @GraphqlField
     */
    public function getUser() : \App\Domain\Model\User
    {
        return $this->getRef('from__user_id__to__table__users__columns__id', 'events_rates');
    }

    /**
     * The setter for the User object bound to this object via the user_id column.
     */
    public function setUser(\App\Domain\Model\User $object) : void
    {
        $this->setRef('from__user_id__to__table__users__columns__id', $object, 'events_rates');
    }

    /**
     * The getter for the "stars_number" column.
     *
     * @return int
     * @GraphqlField
     */
    public function getStarsNumber() : int
    {
        return $this->get('stars_number', 'events_rates');
    }

    /**
     * The setter for the "stars_number" column.
     *
     * @param int $stars_number
     */
    public function setStarsNumber(int $stars_number) : void
    {
        $this->set('stars_number', $stars_number, 'events_rates');
    }

    /**
     * The getter for the "rate_note" column.
     *
     * @return string|null
     * @GraphqlField
     */
    public function getRateNote() : ?string
    {
        return $this->get('rate_note', 'events_rates');
    }

    /**
     * The setter for the "rate_note" column.
     *
     * @param string|null $rate_note
     */
    public function setRateNote(?string $rate_note) : void
    {
        $this->set('rate_note', $rate_note, 'events_rates');
    }

    /**
     * The getter for the "created_at" column.
     *
     * @return \DateTimeImmutable|null
     * @GraphqlField
     */
    public function getCreatedAt() : ?\DateTimeImmutable
    {
        return $this->get('created_at', 'events_rates');
    }

    /**
     * The setter for the "created_at" column.
     *
     * @param \DateTimeImmutable|null $created_at
     */
    public function setCreatedAt(?\DateTimeImmutable $created_at) : void
    {
        $this->set('created_at', $created_at, 'events_rates');
    }

    /**
     * Internal method used to retrieve the list of foreign keys attached to this bean.
     */
    protected static function getForeignKeys(string $tableName) : \TheCodingMachine\TDBM\Schema\ForeignKeys
    {
        if ($tableName === 'events_rates') {
            if (self::$foreignKeys === null) {
                self::$foreignKeys = new ForeignKeys([
                    'from__event_id__to__table__events__columns__id' => [
                        'foreignTable' => 'events',
                        'localColumns' => [
                            'event_id'
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
        if ($stopRecursion) {
            $array['event'] = ['id' => $this->getEvent()->getId()];
        } else {
            $array['event'] = $this->getEvent()->jsonSerialize(true);
        }
        if ($stopRecursion) {
            $array['user'] = ['id' => $this->getUser()->getId()];
        } else {
            $array['user'] = $this->getUser()->jsonSerialize(true);
        }
        $array['starsNumber'] = $this->getStarsNumber();
        $array['rateNote'] = $this->getRateNote();
        $array['createdAt'] = ($date = $this->getCreatedAt()) ? $date->format('c') : null;
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
        return [ 'events_rates' ];
    }

    /**
     * Method called when the bean is removed from database.
     */
    public function onDelete() : void
    {
        parent::onDelete();
        $this->setRef('from__event_id__to__table__events__columns__id', null, 'events_rates');
        $this->setRef('from__user_id__to__table__users__columns__id', null, 'events_rates');
    }

    public function __clone()
    {
        parent::__clone();
    }
}