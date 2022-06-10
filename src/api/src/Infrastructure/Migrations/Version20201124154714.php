<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201124154714 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add work_mode column to users table';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('users')
           ->column('work_mode')->string(20)->null()->graphqlField();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
