<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200615142904 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $db = new TdbmFluidSchema($schema);

        $db->table('questions')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('question')->text()->notNull()->graphqlField()
            ->column('response')->text()->notNull()->graphqlField()
            ->column('theme')->string(50)->notNull()->graphqlField()
            ->column('created_by')->references('users')->null()->default(null)->graphqlField()
            ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_by')->references('users')->null()->default(null)->graphqlField()
            ->column('deleted')->boolean()->notNull()->default(false)->graphqlField();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
