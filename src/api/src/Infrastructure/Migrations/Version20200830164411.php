<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200830164411 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds program.users_have_been_invited and users.has_received_welcome_mail columns';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);

        $db->table('programs')
            ->column('users_have_been_invited')->boolean()->default(false)->graphqlField();
        $db->table('users')
            ->column('has_received_welcome_mail')->boolean()->default(false)->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
