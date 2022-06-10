<?php
/*
 * This file has been automatically generated by TDBM.
 * You can edit this file as it will not be overwritten.
 */

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Enum\ProcedureYouSignStatusEnum;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Logging\LoggableModel;
use App\Domain\Model\Generated\AbstractDocument;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * The Document class maps the 'documents' table in database.
 *
 * @Type
 */
class Document extends AbstractDocument implements LoggableModel
{
    /**
     * @throws InvalidStringValue
     */
    public function setVisibility(string $visibility): void
    {
        $property = 'visibility';
        InvalidStringValue::notBlank($visibility, $property);
        InvalidStringValue::visibility($visibility, $property);
        parent::setVisibility($visibility);
    }

    /**
     * @throws InvalidStringValue
     */
    public function setName(string $name): void
    {
        $property = 'name';
        InvalidStringValue::notBlank($name, $property);
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
     * @Field
     * @InjectUser(for = "$user")
     */
    public function getDocumentsSignersForUser(LoggedUser $user): ?DocumentSigner
    {
        $documentSigner = $this->retrieveManyToOneRelationshipsStorage(
            'documents_signers',
            'from__document_id__to__table__documents_signers__columns__document_id',
            [
                'documents_signers.document_id' => $this->get('id', 'documents'),
                'documents_signers.user_id' => $user->getId(),
            ],
        )->first();

        if ($documentSigner && $documentSigner->getStatusSignature() === ProcedureYouSignStatusEnum::MEMBER_HIDE) {
            return null;
        }

        return $documentSigner;
    }
}
