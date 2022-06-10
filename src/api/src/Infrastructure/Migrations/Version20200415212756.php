<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200415212756 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create new event fields (meeting_place, meeting_room, date_event_end, evaluation_survey)';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('events')
            ->column('meeting_place')->string(255)->null()->graphqlField()
            ->column('meeting_room')->string(255)->null()->graphqlField()
            ->column('date_event_end')->datetimeImmutable()->null()->graphqlField()
            ->column('evaluation_survey')->text()->null()->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
