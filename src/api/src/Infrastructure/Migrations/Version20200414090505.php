<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200414090505 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create event.teams_link column';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('events')
            ->column('teams_link')->string(255)->null()->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
