<?php
/**
 * This file has been automatically generated by TDBM.
 *
 * DO NOT edit this file, as it might be overwritten.
 * If you need to perform changes, edit the Document class instead!
 */

declare(strict_types=1);

namespace App\Domain\Model\Generated;

use App\Domain\Model\FileDescriptor;
use App\Domain\Model\User;
use App\Domain\Model\DocumentCategory;
use App\Domain\Model\Document;
use App\Domain\Model\DocumentSigner;
use App\Domain\Model\Event;
use App\Domain\Model\Program;
use TheCodingMachine\TDBM\AbstractTDBMObject;
use TheCodingMachine\TDBM\ResultIterator;
use TheCodingMachine\TDBM\AlterableResultIterator;
use Ramsey\Uuid\Uuid;
use JsonSerializable;
use TheCodingMachine\TDBM\Schema\ForeignKeys;
use TheCodingMachine\GraphQLite\Annotations\Field as GraphqlField;

/**
 * The AbstractDocument class maps the 'documents' table in database.
 */
abstract class AbstractDocument extends \TheCodingMachine\TDBM\AbstractTDBMObject implements JsonSerializable
{

    /**
     * @var \TheCodingMachine\TDBM\Schema\ForeignKeys
     */
    private static $foreignKeys = null;

    /**
     * The constructor takes all compulsory arguments.
     *
     * @param \App\Domain\Model\User $author
     * @param string $name
     * @param string $description
     * @param string $tags
     * @param string $visibility
     */
    public function __construct(\App\Domain\Model\User $author, string $name, string $description, string $tags, string $visibility)
    {
        parent::__construct();
        $this->setAuthor($author);
        $this->setName($name);
        $this->setDescription($description);
        $this->setTags($tags);
        $this->setVisibility($visibility);
        $this->setId(Uuid::uuid1()->toString());
        $this->setDeleted(false);
        $this->setHidden(false);
        $this->setToBeSigned(false);
        $this->setToBeDisplayedInHomePage(false);
    }

    /**
     * The getter for the "id" column.
     *
     * @return string
     * @GraphqlField (outputType = "ID")
     */
    public function getId() : string
    {
        return $this->get('id', 'documents');
    }

    /**
     * The setter for the "id" column.
     *
     * @param string $id
     */
    public function setId(string $id) : void
    {
        $this->set('id', $id, 'documents');
    }

    /**
     * Returns the FileDescriptor object bound to this object via the
     * file_descriptor_id column.
     *
     * @GraphqlField
     */
    public function getFileDescriptor() : ?\App\Domain\Model\FileDescriptor
    {
        return $this->getRef('from__file_descriptor_id__to__table__file_descriptors__columns__id', 'documents');
    }

    /**
     * The setter for the FileDescriptor object bound to this object via the
     * file_descriptor_id column.
     */
    public function setFileDescriptor(?\App\Domain\Model\FileDescriptor $object) : void
    {
        $this->setRef('from__file_descriptor_id__to__table__file_descriptors__columns__id', $object, 'documents');
    }

    /**
     * Returns the User object bound to this object via the author column.
     *
     * @GraphqlField
     */
    public function getAuthor() : \App\Domain\Model\User
    {
        return $this->getRef('from__author__to__table__users__columns__id', 'documents');
    }

    /**
     * The setter for the User object bound to this object via the author column.
     */
    public function setAuthor(\App\Domain\Model\User $object) : void
    {
        $this->setRef('from__author__to__table__users__columns__id', $object, 'documents');
    }

    /**
     * Returns the User object bound to this object via the created_by column.
     *
     * @GraphqlField
     */
    public function getCreatedBy() : ?\App\Domain\Model\User
    {
        return $this->getRef('from__created_by__to__table__users__columns__id', 'documents');
    }

    /**
     * The setter for the User object bound to this object via the created_by column.
     */
    public function setCreatedBy(?\App\Domain\Model\User $object) : void
    {
        $this->setRef('from__created_by__to__table__users__columns__id', $object, 'documents');
    }

    /**
     * Returns the User object bound to this object via the updated_by column.
     *
     * @GraphqlField
     */
    public function getUpdatedBy() : ?\App\Domain\Model\User
    {
        return $this->getRef('from__updated_by__to__table__users__columns__id', 'documents');
    }

    /**
     * The setter for the User object bound to this object via the updated_by column.
     */
    public function setUpdatedBy(?\App\Domain\Model\User $object) : void
    {
        $this->setRef('from__updated_by__to__table__users__columns__id', $object, 'documents');
    }

    /**
     * Returns the DocumentCategory object bound to this object via the category_id
     * column.
     *
     * @GraphqlField
     */
    public function getCategory() : ?\App\Domain\Model\DocumentCategory
    {
        return $this->getRef('from__category_id__to__table__documents_categories__columns__id', 'documents');
    }

    /**
     * The setter for the DocumentCategory object bound to this object via the
     * category_id column.
     */
    public function setCategory(?\App\Domain\Model\DocumentCategory $object) : void
    {
        $this->setRef('from__category_id__to__table__documents_categories__columns__id', $object, 'documents');
    }

    /**
     * Returns the Document object bound to this object via the livrable_id column.
     *
     * @GraphqlField
     */
    public function getLivrable() : ?\App\Domain\Model\Document
    {
        return $this->getRef('from__livrable_id__to__table__documents__columns__id', 'documents');
    }

    /**
     * The setter for the Document object bound to this object via the livrable_id
     * column.
     */
    public function setLivrable(?\App\Domain\Model\Document $object) : void
    {
        $this->setRef('from__livrable_id__to__table__documents__columns__id', $object, 'documents');
    }

    /**
     * The getter for the "name" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getName() : string
    {
        return $this->get('name', 'documents');
    }

    /**
     * The setter for the "name" column.
     *
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->set('name', $name, 'documents');
    }

    /**
     * The getter for the "description" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getDescription() : string
    {
        return $this->get('description', 'documents');
    }

    /**
     * The setter for the "description" column.
     *
     * @param string $description
     */
    public function setDescription(string $description) : void
    {
        $this->set('description', $description, 'documents');
    }

    /**
     * The getter for the "tags" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getTags() : string
    {
        return $this->get('tags', 'documents');
    }

    /**
     * The setter for the "tags" column.
     *
     * @param string $tags
     */
    public function setTags(string $tags) : void
    {
        $this->set('tags', $tags, 'documents');
    }

    /**
     * The getter for the "visibility" column.
     *
     * @return string
     * @GraphqlField
     */
    public function getVisibility() : string
    {
        return $this->get('visibility', 'documents');
    }

    /**
     * The setter for the "visibility" column.
     *
     * @param string $visibility
     */
    public function setVisibility(string $visibility) : void
    {
        $this->set('visibility', $visibility, 'documents');
    }

    /**
     * The getter for the "elaboration_date" column.
     *
     * @return \DateTimeImmutable|null
     * @GraphqlField
     */
    public function getElaborationDate() : ?\DateTimeImmutable
    {
        return $this->get('elaboration_date', 'documents');
    }

    /**
     * The setter for the "elaboration_date" column.
     *
     * @param \DateTimeImmutable|null $elaboration_date
     */
    public function setElaborationDate(?\DateTimeImmutable $elaboration_date) : void
    {
        $this->set('elaboration_date', $elaboration_date, 'documents');
    }

    /**
     * The getter for the "created_at" column.
     *
     * @return \DateTimeImmutable|null
     * @GraphqlField
     */
    public function getCreatedAt() : ?\DateTimeImmutable
    {
        return $this->get('created_at', 'documents');
    }

    /**
     * The setter for the "created_at" column.
     *
     * @param \DateTimeImmutable|null $created_at
     */
    public function setCreatedAt(?\DateTimeImmutable $created_at) : void
    {
        $this->set('created_at', $created_at, 'documents');
    }

    /**
     * The getter for the "updated_at" column.
     *
     * @return \DateTimeImmutable|null
     * @GraphqlField
     */
    public function getUpdatedAt() : ?\DateTimeImmutable
    {
        return $this->get('updated_at', 'documents');
    }

    /**
     * The setter for the "updated_at" column.
     *
     * @param \DateTimeImmutable|null $updated_at
     */
    public function setUpdatedAt(?\DateTimeImmutable $updated_at) : void
    {
        $this->set('updated_at', $updated_at, 'documents');
    }

    /**
     * The getter for the "deleted" column.
     *
     * @return bool
     * @GraphqlField
     */
    public function getDeleted() : bool
    {
        return $this->get('deleted', 'documents');
    }

    /**
     * The setter for the "deleted" column.
     *
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted) : void
    {
        $this->set('deleted', $deleted, 'documents');
    }

    /**
     * The getter for the "hidden" column.
     *
     * @return bool
     * @GraphqlField
     */
    public function getHidden() : bool
    {
        return $this->get('hidden', 'documents');
    }

    /**
     * The setter for the "hidden" column.
     *
     * @param bool $hidden
     */
    public function setHidden(bool $hidden) : void
    {
        $this->set('hidden', $hidden, 'documents');
    }

    /**
     * The getter for the "to_be_signed" column.
     *
     * @return bool
     * @GraphqlField
     */
    public function getToBeSigned() : bool
    {
        return $this->get('to_be_signed', 'documents');
    }

    /**
     * The setter for the "to_be_signed" column.
     *
     * @param bool $to_be_signed
     */
    public function setToBeSigned(bool $to_be_signed) : void
    {
        $this->set('to_be_signed', $to_be_signed, 'documents');
    }

    /**
     * The getter for the "status_signature" column.
     *
     * @return string|null
     * @GraphqlField
     */
    public function getStatusSignature() : ?string
    {
        return $this->get('status_signature', 'documents');
    }

    /**
     * The setter for the "status_signature" column.
     *
     * @param string|null $status_signature
     */
    public function setStatusSignature(?string $status_signature) : void
    {
        $this->set('status_signature', $status_signature, 'documents');
    }

    /**
     * The getter for the "procedure_id" column.
     *
     * @return string|null
     */
    public function getProcedureId() : ?string
    {
        return $this->get('procedure_id', 'documents');
    }

    /**
     * The setter for the "procedure_id" column.
     *
     * @param string|null $procedure_id
     */
    public function setProcedureId(?string $procedure_id) : void
    {
        $this->set('procedure_id', $procedure_id, 'documents');
    }

    /**
     * The getter for the "to_be_displayed_in_home_page" column.
     *
     * @return bool
     * @GraphqlField
     */
    public function getToBeDisplayedInHomePage() : bool
    {
        return $this->get('to_be_displayed_in_home_page', 'documents');
    }

    /**
     * The setter for the "to_be_displayed_in_home_page" column.
     *
     * @param bool $to_be_displayed_in_home_page
     */
    public function setToBeDisplayedInHomePage(bool $to_be_displayed_in_home_page) : void
    {
        $this->set('to_be_displayed_in_home_page', $to_be_displayed_in_home_page, 'documents');
    }

    /**
     * The getter for the "type" column.
     *
     * @return string|null
     * @GraphqlField
     */
    public function getType() : ?string
    {
        return $this->get('type', 'documents');
    }

    /**
     * The setter for the "type" column.
     *
     * @param string|null $type
     */
    public function setType(?string $type) : void
    {
        $this->set('type', $type, 'documents');
    }

    /**
     * The getter for the "article_link" column.
     *
     * @return string|null
     * @GraphqlField
     */
    public function getArticleLink() : ?string
    {
        return $this->get('article_link', 'documents');
    }

    /**
     * The setter for the "article_link" column.
     *
     * @param string|null $article_link
     */
    public function setArticleLink(?string $article_link) : void
    {
        $this->set('article_link', $article_link, 'documents');
    }

    /**
     * Returns the list of Document pointing to this bean via the livrable_id column.
     *
     * @return Document[]|\TheCodingMachine\TDBM\AlterableResultIterator
     * @GraphqlField
     */
    public function getDocuments() : \TheCodingMachine\TDBM\AlterableResultIterator
    {
        return $this->retrieveManyToOneRelationshipsStorage('documents', 'from__livrable_id__to__table__documents__columns__id', ['documents.livrable_id' => $this->get('id', 'documents')]);
    }

    /**
     * Returns the list of DocumentSigner pointing to this bean via the document_id column.
     *
     * @return DocumentSigner[]|\TheCodingMachine\TDBM\AlterableResultIterator
     */
    public function getDocumentsSigners() : \TheCodingMachine\TDBM\AlterableResultIterator
    {
        return $this->retrieveManyToOneRelationshipsStorage('documents_signers', 'from__document_id__to__table__documents__columns__id', ['documents_signers.document_id' => $this->get('id', 'documents')]);
    }

    /**
     * Returns the list of Event associated to this bean via the documents_events pivot table.
     *
     * @return \App\Domain\Model\Event[]
     * @GraphqlField
     */
    public function getEvents() : array
    {
        return $this->_getRelationships('documents_events.document_id');
    }

    /**
     * Adds a relationship with Event associated to this bean via the documents_events pivot table.
     *
     * @param \App\Domain\Model\Event $event
     */
    public function addEvent(\App\Domain\Model\Event $event) : void
    {
        $this->addRelationship('documents_events', $event);
    }

    /**
     * Deletes the relationship with Event associated to this bean via the documents_events pivot table.
     *
     * @param \App\Domain\Model\Event $event
     */
    public function removeEvent(\App\Domain\Model\Event $event) : void
    {
        $this->_removeRelationship('documents_events', $event);
    }

    /**
     * Returns whether this bean is associated with Event via the documents_events pivot table.
     *
     * @param \App\Domain\Model\Event $event
     * @return bool
     */
    public function hasEvent(\App\Domain\Model\Event $event) : bool
    {
        return $this->hasRelationship('documents_events.document_id', $event);
    }

    /**
     * Sets all relationships with Event associated to this bean via the documents_events pivot table.
     * Exiting relationships will be removed and replaced by the provided relationships.
     *
     * @param \App\Domain\Model\Event[] $events
     * @return void
     */
    public function setEvents(array $events) : void
    {
        $this->setRelationships('documents_events.document_id', $events);
    }

    /**
     * Returns the list of Program associated to this bean via the documents_programs pivot table.
     *
     * @return \App\Domain\Model\Program[]
     * @GraphqlField
     */
    public function getPrograms() : array
    {
        return $this->_getRelationships('documents_programs.document_id');
    }

    /**
     * Adds a relationship with Program associated to this bean via the documents_programs pivot table.
     *
     * @param \App\Domain\Model\Program $program
     */
    public function addProgram(\App\Domain\Model\Program $program) : void
    {
        $this->addRelationship('documents_programs', $program);
    }

    /**
     * Deletes the relationship with Program associated to this bean via the documents_programs pivot table.
     *
     * @param \App\Domain\Model\Program $program
     */
    public function removeProgram(\App\Domain\Model\Program $program) : void
    {
        $this->_removeRelationship('documents_programs', $program);
    }

    /**
     * Returns whether this bean is associated with Program via the documents_programs pivot table.
     *
     * @param \App\Domain\Model\Program $program
     * @return bool
     */
    public function hasProgram(\App\Domain\Model\Program $program) : bool
    {
        return $this->hasRelationship('documents_programs.document_id', $program);
    }

    /**
     * Sets all relationships with Program associated to this bean via the documents_programs pivot table.
     * Exiting relationships will be removed and replaced by the provided relationships.
     *
     * @param \App\Domain\Model\Program[] $programs
     * @return void
     */
    public function setPrograms(array $programs) : void
    {
        $this->setRelationships('documents_programs.document_id', $programs);
    }

    /**
     * Get the paths used for many to many relationships methods.
     *
     * @internal
     */
    public function _getManyToManyRelationshipDescriptor(string $pathKey) : \TheCodingMachine\TDBM\Utils\ManyToManyRelationshipPathDescriptor
    {
        switch ($pathKey) {
            case 'documents_events.document_id':
                return new \TheCodingMachine\TDBM\Utils\ManyToManyRelationshipPathDescriptor('events', 'documents_events', ['id'], ['event_id'], ['document_id']);
            case 'documents_programs.document_id':
                return new \TheCodingMachine\TDBM\Utils\ManyToManyRelationshipPathDescriptor('programs', 'documents_programs', ['id'], ['program_id'], ['document_id']);
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
        return array_merge(parent::_getManyToManyRelationshipDescriptorKeys(), ['documents_events.document_id', 'documents_programs.document_id']);
    }

    /**
     * Internal method used to retrieve the list of foreign keys attached to this bean.
     */
    protected static function getForeignKeys(string $tableName) : \TheCodingMachine\TDBM\Schema\ForeignKeys
    {
        if ($tableName === 'documents') {
            if (self::$foreignKeys === null) {
                self::$foreignKeys = new ForeignKeys([
                    'from__author__to__table__users__columns__id' => [
                        'foreignTable' => 'users',
                        'localColumns' => [
                            'author'
                        ],
                        'foreignColumns' => [
                            'id'
                        ]
                    ],
                    'from__category_id__to__table__documents_categories__columns__id' => [
                        'foreignTable' => 'documents_categories',
                        'localColumns' => [
                            'category_id'
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
                    'from__file_descriptor_id__to__table__file_descriptors__columns__id' => [
                        'foreignTable' => 'file_descriptors',
                        'localColumns' => [
                            'file_descriptor_id'
                        ],
                        'foreignColumns' => [
                            'id'
                        ]
                    ],
                    'from__livrable_id__to__table__documents__columns__id' => [
                        'foreignTable' => 'documents',
                        'localColumns' => [
                            'livrable_id'
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
            $array['fileDescriptor'] = ($object = $this->getFileDescriptor()) ? ['id' => $object->getId()] : null;
        } else {
            $array['fileDescriptor'] = ($object = $this->getFileDescriptor()) ? $object->jsonSerialize(true) : null;
        }
        if ($stopRecursion) {
            $array['author'] = ['id' => $this->getAuthor()->getId()];
        } else {
            $array['author'] = $this->getAuthor()->jsonSerialize(true);
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
            $array['category'] = ($object = $this->getCategory()) ? ['id' => $object->getId()] : null;
        } else {
            $array['category'] = ($object = $this->getCategory()) ? $object->jsonSerialize(true) : null;
        }
        if ($stopRecursion) {
            $array['livrable'] = ($object = $this->getLivrable()) ? ['id' => $object->getId()] : null;
        } else {
            $array['livrable'] = ($object = $this->getLivrable()) ? $object->jsonSerialize(true) : null;
        }
        $array['name'] = $this->getName();
        $array['description'] = $this->getDescription();
        $array['tags'] = $this->getTags();
        $array['visibility'] = $this->getVisibility();
        $array['elaborationDate'] = ($date = $this->getElaborationDate()) ? $date->format('c') : null;
        $array['createdAt'] = ($date = $this->getCreatedAt()) ? $date->format('c') : null;
        $array['updatedAt'] = ($date = $this->getUpdatedAt()) ? $date->format('c') : null;
        $array['deleted'] = $this->getDeleted();
        $array['hidden'] = $this->getHidden();
        $array['toBeSigned'] = $this->getToBeSigned();
        $array['statusSignature'] = $this->getStatusSignature();
        $array['procedureId'] = $this->getProcedureId();
        $array['toBeDisplayedInHomePage'] = $this->getToBeDisplayedInHomePage();
        $array['type'] = $this->getType();
        $array['articleLink'] = $this->getArticleLink();
        if (!$stopRecursion) {
            $array['events'] = array_map(function (Event $object) {
                return $object->jsonSerialize(true);
            }, $this->getEvents());
        };
        if (!$stopRecursion) {
            $array['programs'] = array_map(function (Program $object) {
                return $object->jsonSerialize(true);
            }, $this->getPrograms());
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
        return [ 'documents' ];
    }

    /**
     * Method called when the bean is removed from database.
     */
    public function onDelete() : void
    {
        parent::onDelete();
        $this->setRef('from__file_descriptor_id__to__table__file_descriptors__columns__id', null, 'documents');
        $this->setRef('from__author__to__table__users__columns__id', null, 'documents');
        $this->setRef('from__created_by__to__table__users__columns__id', null, 'documents');
        $this->setRef('from__updated_by__to__table__users__columns__id', null, 'documents');
        $this->setRef('from__category_id__to__table__documents_categories__columns__id', null, 'documents');
        $this->setRef('from__livrable_id__to__table__documents__columns__id', null, 'documents');
    }

    public function __clone()
    {
        $this->getEvents();

        $this->getPrograms();

        parent::__clone();
        $this->setId(Uuid::uuid1()->toString());
    }
}
