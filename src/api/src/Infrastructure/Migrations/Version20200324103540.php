<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200324103540 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create Program Coaching Individuals';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('program_coaching_individuals')
            ->column('id')->references('programs')->primaryKey()->comment('@UUID')->graphqlField()
            ->column('first_name')->string(255)->notNull()->graphqlField()
            ->column('last_name')->string(255)->notNull()->graphqlField()
            ->column('email')->string(255)->notNull()->graphqlField()
            ->column('phone')->string(255)->notNull()->graphqlField();
        $db->junctionTable('documents', 'programs')->graphqlField();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
