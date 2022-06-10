<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200611102632 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('programs_coaches')
            ->column('program_id')->references('programs')->notNull()->graphqlField()
            ->column('coach_id')->references('users')->notNull()->graphqlField();

        $db->table('users')
            ->column('service')->string(255)->null()->graphqlField()
            ->column('ville')->string(255)->null()->graphqlField()
            ->column('department')->string(255)->null()->graphqlField()
            ->column('post_code')->string(10)->null()->graphqlField();

        $schema->getTable('programs')->dropIndex('IDX_F14965453C105691');
        $schema->getTable('programs')->removeForeignKey('FK_F14965453C105691');
    }

    public function postUp(Schema $schema) : void
    {
        $this->connection->executeQuery("
            INSERT INTO programs_coaches (program_id, coach_id)
            SELECT id, coach_id FROM programs
        ");

        $this->connection->executeQuery("
            ALTER TABLE programs DROP COLUMN coach_id
        ");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
