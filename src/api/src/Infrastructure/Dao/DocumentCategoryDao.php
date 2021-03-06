<?php
/*
 * This file has been automatically generated by TDBM.
 * You can edit this file as it will not be overwritten.
 */

declare(strict_types=1);

namespace App\Infrastructure\Dao;

use App\Domain\Enum\DocumentCategoryEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\DocumentCategory;
use App\Domain\Model\User;
use App\Domain\Repository\DocumentCategoryRepository;
use App\Infrastructure\Dao\Generated\AbstractDocumentCategoryDao;
use TheCodingMachine\TDBM\ResultIterator;

/**
 * The DocumentCategoryDao class will maintain the persistence of DocumentCategory class into the documents_categories table.
 */
class DocumentCategoryDao extends AbstractDocumentCategoryDao implements DocumentCategoryRepository
{
    public function getAll(User $currentUser): ResultIterator
    {
        return $this->find(
            $currentUser->getType()->getId() === UserTypeEnum::CANDIDATE ? 'id != :toolboxCategory' : '',
            ['toolboxCategory' => DocumentCategoryEnum::TOOLBOX],
            'order_view ASC, label ASC',
        );
    }

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): DocumentCategory
    {
        $documentCategory = $this->findOne(['id' => $id]);
        if ($documentCategory === null) {
            throw new NotFound(DocumentCategory::class, ['id' => $id]);
        }

        return $documentCategory;
    }
}
