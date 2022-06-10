<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200422193549 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Modify memo field and add period field';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('events')
            ->column('memo')->text()->null()->graphqlField();
        $db->table('programs')
            ->column('period')->integer()->null()->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
