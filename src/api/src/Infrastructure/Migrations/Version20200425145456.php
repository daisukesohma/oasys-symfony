<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200425145456 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('documents')
            ->column('file_descriptor_id')->references('file_descriptors')->null()->graphqlField()
            ->column('to_be_displayed_in_home_page')->boolean()->default(false)->graphqlField()
            ->column('type')->string(20)->null()->graphqlField()
            ->column('article_link')->string(255)->null()->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
