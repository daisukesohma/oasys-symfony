<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200628153449 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create events_rates table';
    }

    public function up(Schema $schema) : void
    {
       $db = new TdbmFluidSchema($schema);
       $db->table('events_rates')
           ->column('event_id')->references('events')->comment('@UUID')->graphqlField()->then()
           ->column('user_id')->references('users')->comment('@UUID')->graphqlField()->then()
           ->primaryKey(['event_id', 'user_id'])
           ->column('stars_number')->integer()->notNull()->graphqlField()
           ->column('rate_note')->text()->null()->graphqlField()
           ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
