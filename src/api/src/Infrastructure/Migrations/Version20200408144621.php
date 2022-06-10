<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200408144621 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add memo to events table and create todos';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('events')
            ->column('memo')->string()->null()->graphqlField();

        $db->table('todos')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('label')->text()->notNull()->graphqlField()
            ->column('done')->boolean()->default(false)->graphqlField()
            ->column('program_id')->references('programs')->notNull()->graphqlField()
            ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('created_by')->references('users')->null()->default(null)->graphqlField()
            ->column('updated_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_by')->references('users')->null()->default(null)->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
