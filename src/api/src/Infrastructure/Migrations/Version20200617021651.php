<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200617021651 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add users.appointment_booked and events.max_number_invites column';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('users')
            ->column('appointment_booked')->boolean()->default(true)->graphqlField()
            ->column('cv_file_id')->references('file_descriptors')->null()->graphqlField();

        $db->table('events')
            ->column('number_max_invites')->integer()->null()->graphqlField();
    }

    public function down(Schema $schema) : void
    {

    }
}
