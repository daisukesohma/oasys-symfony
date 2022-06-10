<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200529083332 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Removes objective_contract_id field';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->getTable('events');
        $table->dropIndex('IDX_5387574A805128A8');
        $table->removeForeignKey('FK_5387574A805128A8');
        $table->dropColumn('objective_contract_id');
    }


    public function down(Schema $schema) : void
    {

    }
}
