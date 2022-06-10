<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200518104601 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add N+1 columns to users table';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('users')
            ->column('n_first_name')->string(255)->null()->graphqlField()
            ->column('n_last_name')->string(255)->null()->graphqlField()
            ->column('n_email')->string(255)->null()->graphqlField()
            ->column('n_phone')->string(255)->null()->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
