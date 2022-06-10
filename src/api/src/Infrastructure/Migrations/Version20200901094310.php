<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200901094310 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add company_id field for programs table';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);

        $db->table('programs')
            ->column('company_id')->references('companies')->null()->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
