<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200622072212 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create table villes_france';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('villes_france')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('code_postal')->string(255)->graphqlField()
            ->column('department_number')->string(255)->graphqlField()
            ->column('department_name')->string(255)->graphqlField()
            ->column('common_name')->string(255)->graphqlField()
            ->column('region_name')->string(255)->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
