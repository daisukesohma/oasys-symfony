<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use App\Domain\Enum\ProgramTypeEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200616092905 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create program_pic';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('program_pic')
            ->column('id')->references('programs')->primaryKey()->comment('@UUID')->graphqlField();

        $db->table('users')
            ->column('birth_date')->date()->null()->graphqlField();
    }

    public function postUp(Schema $schema) : void
    {
        $this->connection->executeQuery("
            INSERT INTO program_pic (id)
            SELECT id FROM programs WHERE type = :type
        ", ['type' => ProgramTypeEnum::PIC]);
    }

    public function down(Schema $schema) : void
    {
    }
}
