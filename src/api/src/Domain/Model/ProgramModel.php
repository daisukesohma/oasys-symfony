<?php
/*
 * This file has been automatically generated by TDBM.
 * You can edit this file as it will not be overwritten.
 */

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Logging\LoggableModel;
use App\Domain\Model\Generated\AbstractProgramModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\TDBM\AlterableResultIterator;

/**
 * The ProgramModel class maps the 'program_models' table in database.
 *
 * @Type
 */

class ProgramModel extends AbstractProgramModel implements LoggableModel
{
    /**
     * @throws InvalidStringValue
     */
    public function setName(string $name): void
    {
        $property = 'name';
        InvalidStringValue::notBlank($name, $property);
        InvalidStringValue::length($name, 1, 255, $property);
        parent::setName($name);
    }

    /**
     * @throws InvalidStringValue
     */
    public function setDescription(string $description): void
    {
        $property = 'description';
        InvalidStringValue::notBlank($description, $property);
        parent::setDescription($description);
    }

    /**
     * @return AlterableResultIterator|EventModel[]
     *
     * @Field
     */
    public function getEventModels(): AlterableResultIterator
    {
        return $this->retrieveManyToOneRelationshipsStorage(
            'event_models',
            'from__program_model_id__to__table__program_models__columns__id',
            [
                'event_models.program_model_id' => $this->get('id', 'program_models'),
                'event_models.deleted' => 0,
            ],
        );
    }
}
