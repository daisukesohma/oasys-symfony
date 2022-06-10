<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use App\Domain\Enum\DocumentCategoryEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201012121246 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create documents_categories table';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);

        $db->table('documents_categories')
            ->column('id')->string(50)->primaryKey()->graphqlField()
            ->column('label')->string(50)->notNull()->graphqlField();

        $db->table('documents')
            ->column('category_id')->references('documents_categories')->null()->graphqlField()
            ->column('livrable_id')->references('documents')->null()->graphqlField();
    }

    public function postUp(Schema $schema): void
    {
        parent::postUp($schema);

        foreach (DocumentCategoryEnum::values() as $id => $label) {
            $this->connection->insert('documents_categories', [
                'id' => $id,
                'label' => $label,
            ]);
        }
    }

    public function down(Schema $schema) : void
    {
    }
}
