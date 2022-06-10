<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use App\Domain\Enum\CoachSpecialityEnum;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201103120259 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds generalist coach speciality';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('events')
            ->column('coach_speciality')->references('coach_specialities')->null()->graphqlField();

        $statement = $this->connection->executeQuery('
            SELECT id
            FROM coach_specialities
            WHERE id = :generalist
        ', ['generalist' => CoachSpecialityEnum::GENERALIST]);

        if (! $statement->fetch(FetchMode::ASSOCIATIVE)) {
            $this->connection->insert('coach_specialities', [
                'id' => CoachSpecialityEnum::GENERALIST,
                'label' => CoachSpecialityEnum::GENERALIST_LABEL,
            ]);
        }
    }

    public function down(Schema $schema) : void
    {
    }
}
