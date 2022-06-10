<?php
/**
 * This file has been automatically generated by TDBM.
 *
 * DO NOT edit this file, as it might be overwritten.
 * If you need to perform changes, edit the Event class instead!
 */

declare(strict_types=1);

namespace App\Domain\Model\Generated;

use App\Domain\Model\User;
use App\Domain\Model\EventModel;
use App\Domain\Model\Program;
use App\Domain\Model\CoachSpeciality;
use App\Domain\Model\EventRate;
use App\Domain\Model\Document;
use TheCodingMachine\TDBM\AbstractTDBMObject;
use TheCodingMachine\TDBM\ResultIterator;
use TheCodingMachine\TDBM\AlterableResultIterator;
use Ramsey\Uuid\Uuid;
use JsonSerializable;
use TheCodingMachine\TDBM\Schema\ForeignKeys;
use TheCodingMachine\GraphQLite\Annotations\Field as GraphqlField;

/**
 * The AbstractEvent class maps the 'events' table in database.
 */
abstract class AbstractEvent extends \TheCodingMachine\TDBM\AbstractTDBMObject implements JsonSerializable
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
     */
    public function __construct(string $name, string $description, string $type)
    {
        parent::__construct();
        $this->setName($name);
        $this->setDescription($description);
        $this->setType($type);
        $this->setId(Uuid::uuid1()->toString());
        $this->setStatus('created');
    }

    /**
     * The getter for the "id" column.
     *
     * @return string
     * @GraphqlField (outputType = "ID")
     */
    public function getId() : string
    {
        return $this->get('id', 'events');
    }

    /**
     * The setter for the "id" column.
     *
     * @param string $id
     */
    public function setId(string $id) : void
    {
        $this->set('id', $id, 'events');
    }

    /**
     * Returns the User object bound to this object via the organizer column.
     *
     * @GraphqlField
     */
    public function getOrganizer() : ?\App\Domain\Model\User
    {
        return $this->getRef('from__organizer__to__table__users__columns__id', 'events');
    }

    /**
     * The setter for the User object bound to this object via the organizer column.
     */
    public function setOrganizer(?\App\Domain\Model\User $object) : void
    {
        $this->setRef('from__organizer__to__table__users__columns__id', $object, 'events');
    }

    /**
     * Returns the EventModel object bound to this object via the event_model_id
     * column.
     *
     * @GraphqlField
     */
    public function getEventModel() : ?\App\Domain\Model\EventModel
    {
        return $this->getRef('from__event_model_id__to__table__event_models__columns__id', 'events');
    }

    /**
     * The setter for the EventModel object bound to this object via the event_model_id
     * column.
     */
    public function setEventModel(?\App\Domain\Model\EventModel $object) : void
    {
        $this->setRef('from__event_model_id__to__table__event_models__columns__id', $object, 'events');
    }

    /**
     * Returns the Program object bound to this object via the program_id column.
     *
     * @GraphqlField
     */
    public function getProgram() : ?\App\Domain\Model\Program
    {
        return $this->getRef('from__program_id__to__table__programs__columns__id', 'events');
    }

    /**
     * The setter for the Program object bound to this object via the program_id
     * column.
     */
    public function setProgram(?\App\Domain\Model\Program $object) : void
    {
        $this->setRef('from__program_id__to__table__programs__columns__id', $object, 'events');
    }

    /**
     * Returns the User object bound to this object via the created_by column.
     *
     * @GraphqlField
     */
    public function getCreatedBy() : ?\App\Domain\Model\User
    {
        return $this->getRef('from__created_by__to__table__users__columns__id', 'events');
    }

    /**
     * The setter for the User object bound to this object via the created_by column.
     */
    public function setCreatedBy(?\App\Domain\Model\User $object) : void
    {
        $this->setRef('from__created_by__to__table__users__columns__id', $object, 'events');
    }

    /**
     * Returns the User object bound to this object via the updated_by column.
     *
     * @GraphqlField
     */
    public function getUpdatedBy() : ?\App\Domain\Model\User
    {
        return $this->getRef('from__updated_by__to__table__users__columns__id', 'events');
    }

    /**
     * The setter for the User object bound to this object via the updated_by column.
     */
    public function setUpdatedBy(?\App\Domain\Model\User $object) : void
    {
        $this->setRef('from__updated_by__to__table__users__columns__id', $object, 'events');
    }

    /**
     * Returns the CoachSpeciality object bound to this object via the coach_speciality
     * column.
     *
     * @GraphqlField
     */
    public function getCoachSpeciality() : ?\App\Domain\Model\CoachSpeciality
    {
        return $this->getRef('from__coach_speciality__to__table__coach_specialities__columns__id', 'events');
    }

    /**
     * The setter for the CoachSpeciality object bound to this object via the
     * coach_speciality column.
     */
    public function setCoachSpeciality(?\App\Domain\Model\CoachSpeciality $object) : void
    {
        $this->setRef('from__coach_speciality__to__table__coach_specialities__columns__id', $object, 'events');
    }

    /**
     * The getter for the "name" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getName() : string
    {
        return $this->get('name', 'events');
    }

    /**
     * The setter for the "name" column.
     *
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->set('name', $name, 'events');
    }

    /**
     * The getter for the "description" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getDescription() : string
    {
        return $this->get('description', 'events');
    }

    /**
     * The setter for the "description" column.
     *
     * @param string $description
     */
    public function setDescription(string $description) : void
    {
        $this->set('description', $description, 'events');
    }

    /**
     * The getter for the "type" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getType() : string
    {
        return $this->get('type', 'events');
    }

    /**
     * The setter for the "type" column.
     *
     * @param string $type
     */
    public function setType(string $type) : void
    {
        $this->set('type', $type, 'events');
    }

    /**
     * The getter for the "date_event" column.
     *
     * @return \DateTimeImmutable|null
     * @GraphqlField
     */
    public function getDateEvent() : ?\DateTimeImmutable
    {
        return $this->get('date_event', 'events');
    }

    /**
     * The setter for the "date_event" column.
     *
     * @param \DateTimeImmutable|null $date_event
     */
    public function setDateEvent(?\DateTimeImmutable $date_event) : void
    {
        $this->set('date_event', $date_event, 'events');
    }

    /**
     * The getter for the "status" column.
     *
     * @return string|null
     * @GraphqlField
     */
    public function getStatus() : ?string
    {
        return $this->get('status', 'events');
    }

    /**
     * The setter for the "status" column.
     *
     * @param string|null $status
     */
    public function setStatus(?string $status) : void
    {
        $this->set('status', $status, 'events');
    }

    /**
     * The getter for the "created_at" column.
     *
     * @return \DateTimeImmutable|null
     * @GraphqlField
     */
    public function getCreatedAt() : ?\DateTimeImmutable
    {
        return $this->get('created_at', 'events');
    }

    /**
     * The setter for the "created_at" column.
     *
     * @param \DateTimeImmutable|null $created_at
     */
    public function setCreatedAt(?\DateTimeImmutable $created_at) : void
    {
        $this->set('created_at', $created_at, 'events');
    }

    /**
     * The getter for the "updated_at" column.
     *
     * @return \DateTimeImmutable|null
     * @GraphqlField
     */
    public function getUpdatedAt() : ?\DateTimeImmutable
    {
        return $this->get('updated_at', 'events');
    }

    /**
     * The setter for the "updated_at" column.
     *
     * @param \DateTimeImmutable|null $updated_at
     */
    public function setUpdatedAt(?\DateTimeImmutable $updated_at) : void
    {
        $this->set('updated_at', $updated_at, 'events');
    }

    /**
     * The getter for the "memo" column.
     *
     * @return string|null
     * @GraphqlField
     */
    public function getMemo() : ?string
    {
        return $this->get('memo', 'events');
    }

    /**
     * The setter for the "memo" column.
     *
     * @param string|null $memo
     */
    public function setMemo(?string $memo) : void
    {
        $this->set('memo', $memo, 'events');
    }

    /**
     * The getter for the "teams_link" column.
     *
     * @return string|null
     * @GraphqlField
     */
    public function getTeamsLink() : ?string
    {
        return $this->get('teams_link', 'events');
    }

    /**
     * The setter for the "teams_link" column.
     *
     * @param string|null $teams_link
     */
    public function setTeamsLink(?string $teams_link) : void
    {
        $this->set('teams_link', $teams_link, 'events');
    }

    /**
     * The getter for the "meeting_place" column.
     *
     * @return string|null
     * @GraphqlField
     */
    public function getMeetingPlace() : ?string
    {
        return $this->get('meeting_place', 'events');
    }

    /**
     * The setter for the "meeting_place" column.
     *
     * @param string|null $meeting_place
     */
    public function setMeetingPlace(?string $meeting_place) : void
    {
        $this->set('meeting_place', $meeting_place, 'events');
    }

    /**
     * The getter for the "meeting_room" column.
     *
     * @return string|null
     * @GraphqlField
     */
    public function getMeetingRoom() : ?string
    {
        return $this->get('meeting_room', 'events');
    }

    /**
     * The setter for the "meeting_room" column.
     *
     * @param string|null $meeting_room
     */
    public function setMeetingRoom(?string $meeting_room) : void
    {
        $this->set('meeting_room', $meeting_room, 'events');
    }

    /**
     * The getter for the "date_event_end" column.
     *
     * @return \DateTimeImmutable|null
     * @GraphqlField
     */
    public function getDateEventEnd() : ?\DateTimeImmutable
    {
        return $this->get('date_event_end', 'events');
    }

    /**
     * The setter for the "date_event_end" column.
     *
     * @param \DateTimeImmutable|null $date_event_end
     */
    public function setDateEventEnd(?\DateTimeImmutable $date_event_end) : void
    {
        $this->set('date_event_end', $date_event_end, 'events');
    }

    /**
     * The getter for the "evaluation_survey" column.
     *
     * @return string|null
     * @GraphqlField
     */
    public function getEvaluationSurvey() : ?string
    {
        return $this->get('evaluation_survey', 'events');
    }

    /**
     * The setter for the "evaluation_survey" column.
     *
     * @param string|null $evaluation_survey
     */
    public function setEvaluationSurvey(?string $evaluation_survey) : void
    {
        $this->set('evaluation_survey', $evaluation_survey, 'events');
    }

    /**
     * The getter for the "number_max_invites" column.
     *
     * @return int|null
     * @GraphqlField
     */
    public function getNumberMaxInvites() : ?int
    {
        return $this->get('number_max_invites', 'events');
    }

    /**
     * The setter for the "number_max_invites" column.
     *
     * @param int|null $number_max_invites
     */
    public function setNumberMaxInvites(?int $number_max_invites) : void
    {
        $this->set('number_max_invites', $number_max_invites, 'events');
    }

    /**
     * Returns the list of EventRate pointing to this bean via the event_id column.
     *
     * @return EventRate[]|\TheCodingMachine\TDBM\AlterableResultIterator
     * @GraphqlField
     */
    public function getEventsRates() : \TheCodingMachine\TDBM\AlterableResultIterator
    {
        return $this->retrieveManyToOneRelationshipsStorage('events_rates', 'from__event_id__to__table__events__columns__id', ['events_rates.event_id' => $this->get('id', 'events')]);
    }

    /**
     * Returns the list of Document associated to this bean via the documents_events pivot table.
     *
     * @return \App\Domain\Model\Document[]
     * @GraphqlField
     */
    public function getDocuments() : array
    {
        return $this->_getRelationships('documents_events.event_id');
    }

    /**
     * Adds a relationship with Document associated to this bean via the documents_events pivot table.
     *
     * @param \App\Domain\Model\Document $document
     */
    public function addDocument(\App\Domain\Model\Document $document) : void
    {
        $this->addRelationship('documents_events', $document);
    }

    /**
     * Deletes the relationship with Document associated to this bean via the documents_events pivot table.
     *
     * @param \App\Domain\Model\Document $document
     */
    public function removeDocument(\App\Domain\Model\Document $document) : void
    {
        $this->_removeRelationship('documents_events', $document);
    }

    /**
     * Returns whether this bean is associated with Document via the documents_events pivot table.
     *
     * @param \App\Domain\Model\Document $document
     * @return bool
     */
    public function hasDocument(\App\Domain\Model\Document $document) : bool
    {
        return $this->hasRelationship('documents_events.event_id', $document);
    }

    /**
     * Sets all relationships with Document associated to this bean via the documents_events pivot table.
     * Exiting relationships will be removed and replaced by the provided relationships.
     *
     * @param \App\Domain\Model\Document[] $documents
     * @return void
     */
    public function setDocuments(array $documents) : void
    {
        $this->setRelationships('documents_events.event_id', $documents);
    }

    /**
     * Returns the list of User associated to this bean via the events_users pivot table.
     *
     * @return \App\Domain\Model\User[]
     * @GraphqlField
     */
    public function getUsers() : array
    {
        return $this->_getRelationships('events_users.event_id');
    }

    /**
     * Adds a relationship with User associated to this bean via the events_users pivot table.
     *
     * @param \App\Domain\Model\User $user
     */
    public function addUser(\App\Domain\Model\User $user) : void
    {
        $this->addRelationship('events_users', $user);
    }

    /**
     * Deletes the relationship with User associated to this bean via the events_users pivot table.
     *
     * @param \App\Domain\Model\User $user
     */
    public function removeUser(\App\Domain\Model\User $user) : void
    {
        $this->_removeRelationship('events_users', $user);
    }

    /**
     * Returns whether this bean is associated with User via the events_users pivot table.
     *
     * @param \App\Domain\Model\User $user
     * @return bool
     */
    public function hasUser(\App\Domain\Model\User $user) : bool
    {
        return $this->hasRelationship('events_users.event_id', $user);
    }

    /**
     * Sets all relationships with User associated to this bean via the events_users pivot table.
     * Exiting relationships will be removed and replaced by the provided relationships.
     *
     * @param \App\Domain\Model\User[] $users
     * @return void
     */
    public function setUsers(array $users) : void
    {
        $this->setRelationships('events_users.event_id', $users);
    }

    /**
     * Get the paths used for many to many relationships methods.
     *
     * @internal
     */
    public function _getManyToManyRelationshipDescriptor(string $pathKey) : \TheCodingMachine\TDBM\Utils\ManyToManyRelationshipPathDescriptor
    {
        switch ($pathKey) {
            case 'documents_events.event_id':
                return new \TheCodingMachine\TDBM\Utils\ManyToManyRelationshipPathDescriptor('documents', 'documents_events', ['id'], ['document_id'], ['event_id']);
            case 'events_users.event_id':
                return new \TheCodingMachine\TDBM\Utils\ManyToManyRelationshipPathDescriptor('users', 'events_users', ['id'], ['user_id'], ['event_id']);
            default:
                return parent::_getManyToManyRelationshipDescriptor($pathKey);
        }
    }

    /**
     * Returns the list of keys supported for many to many relationships
     *
     * @internal
     * @return string[]
     */
    public function _getManyToManyRelationshipDescriptorKeys() : array
    {
        return array_merge(parent::_getManyToManyRelationshipDescriptorKeys(), ['documents_events.event_id', 'events_users.event_id']);
    }

    /**
     * Internal method used to retrieve the list of foreign keys attached to this bean.
     */
    protected static function getForeignKeys(string $tableName) : \TheCodingMachine\TDBM\Schema\ForeignKeys
    {
        if ($tableName === 'events') {
            if (self::$foreignKeys === null) {
                self::$foreignKeys = new ForeignKeys([
                    'from__coach_speciality__to__table__coach_specialities__columns__id' => [
                        'foreignTable' => 'coach_specialities',
                        'localColumns' => [
                            'coach_speciality'
                        ],
                        'foreignColumns' => [
                            'id'
                        ]
                    ],
                    'from__created_by__to__table__users__columns__id' => [
                        'foreignTable' => 'users',
                        'localColumns' => [
                            'created_by'
                        ],
                        'foreignColumns' => [
                            'id'
                        ]
                    ],
                    'from__event_model_id__to__table__event_models__columns__id' => [
                        'foreignTable' => 'event_models',
                        'localColumns' => [
                            'event_model_id'
                        ],
                        'foreignColumns' => [
                            'id'
                        ]
                    ],
                    'from__organizer__to__table__users__columns__id' => [
                        'foreignTable' => 'users',
                        'localColumns' => [
                            'organizer'
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
            $array['organizer'] = ($object = $this->getOrganizer()) ? ['id' => $object->getId()] : null;
        } else {
            $array['organizer'] = ($object = $this->getOrganizer()) ? $object->jsonSerialize(true) : null;
        }
        if ($stopRecursion) {
            $array['eventModel'] = ($object = $this->getEventModel()) ? ['id' => $object->getId()] : null;
        } else {
            $array['eventModel'] = ($object = $this->getEventModel()) ? $object->jsonSerialize(true) : null;
        }
        if ($stopRecursion) {
            $array['program'] = ($object = $this->getProgram()) ? ['id' => $object->getId()] : null;
        } else {
            $array['program'] = ($object = $this->getProgram()) ? $object->jsonSerialize(true) : null;
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
            $array['coachSpeciality'] = ($object = $this->getCoachSpeciality()) ? ['id' => $object->getId()] : null;
        } else {
            $array['coachSpeciality'] = ($object = $this->getCoachSpeciality()) ? $object->jsonSerialize(true) : null;
        }
        $array['name'] = $this->getName();
        $array['description'] = $this->getDescription();
        $array['type'] = $this->getType();
        $array['dateEvent'] = ($date = $this->getDateEvent()) ? $date->format('c') : null;
        $array['status'] = $this->getStatus();
        $array['createdAt'] = ($date = $this->getCreatedAt()) ? $date->format('c') : null;
        $array['updatedAt'] = ($date = $this->getUpdatedAt()) ? $date->format('c') : null;
        $array['memo'] = $this->getMemo();
        $array['teamsLink'] = $this->getTeamsLink();
        $array['meetingPlace'] = $this->getMeetingPlace();
        $array['meetingRoom'] = $this->getMeetingRoom();
        $array['dateEventEnd'] = ($date = $this->getDateEventEnd()) ? $date->format('c') : null;
        $array['evaluationSurvey'] = $this->getEvaluationSurvey();
        $array['numberMaxInvites'] = $this->getNumberMaxInvites();
        if (!$stopRecursion) {
            $array['documents'] = array_map(function (Document $object) {
                return $object->jsonSerialize(true);
            }, $this->getDocuments());
        };
        if (!$stopRecursion) {
            $array['users'] = array_map(function (User $object) {
                return $object->jsonSerialize(true);
            }, $this->getUsers());
        };
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
        return [ 'events' ];
    }

    /**
     * Method called when the bean is removed from database.
     */
    public function onDelete() : void
    {
        parent::onDelete();
        $this->setRef('from__organizer__to__table__users__columns__id', null, 'events');
        $this->setRef('from__event_model_id__to__table__event_models__columns__id', null, 'events');
        $this->setRef('from__program_id__to__table__programs__columns__id', null, 'events');
        $this->setRef('from__created_by__to__table__users__columns__id', null, 'events');
        $this->setRef('from__updated_by__to__table__users__columns__id', null, 'events');
        $this->setRef('from__coach_speciality__to__table__coach_specialities__columns__id', null, 'events');
    }

    public function __clone()
    {
        $this->getDocuments();

        $this->getUsers();

        parent::__clone();
        $this->setId(Uuid::uuid1()->toString());
    }
}