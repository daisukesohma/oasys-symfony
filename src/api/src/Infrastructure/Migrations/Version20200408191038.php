<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200408191038 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('documents')
            ->column('hidden')->boolean()->notNull()->default(false)->graphqlField();

    }

    public function down(Schema $schema) : void
    {
    }
}
