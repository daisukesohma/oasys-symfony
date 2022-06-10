<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use App\Domain\Enum\CoachSpecialityEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200831152234 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create coach_speciality tables';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);

        $db->table('coach_specialities')
            ->column('id')->string(50)->primaryKey()->graphqlField()
            ->column('label')->string(255)->notNull()->graphqlField();

        $db->table('users')
            ->column('coach_speciality')->references('coach_specialities')->null()->graphqlField()
            ->column('has_been_transferred')->boolean()->default(false)->graphqlField();
    }

    public function postUp(Schema $schema): void {
        foreach (CoachSpecialityEnum::values() as $id => $label) {
            $this->connection->insert('coach_specialities', [
                'id' => $id,
                'label' => $label,
            ]);
        }
    }

    public function down(Schema $schema) : void
    {
    }
}
