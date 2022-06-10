<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201126093733 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add functions_services_file_id column';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('companies')
           ->column('functions_services_file_id')->references('file_descriptors')->null()->default(null)->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
