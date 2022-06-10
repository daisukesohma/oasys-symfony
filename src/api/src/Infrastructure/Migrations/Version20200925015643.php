<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200925015643 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add address fields for users';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('users')
            ->column('user_code_postal')->string(255)->null()->graphqlField()
            ->column('user_department')->string(255)->null()->graphqlField()
            ->column('user_city')->string(255)->null()->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
