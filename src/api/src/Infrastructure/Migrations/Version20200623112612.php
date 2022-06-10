<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use App\Domain\Enum\RightEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200623112612 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create ROLE_BOOK_APPOINTMENT right';
    }

    public function up(Schema $schema) : void
    {
        $this->connection
            ->insert(
                'rights',
                [
                    'id' => Uuid::uuid4()->toString(),
                    'name' => "Permet de reprendre rendez-vous",
                    'order_view' => 24,
                    'code' => RightEnum::ROLE_BOOK_APPOINTMENT,
                ]
            );
    }

    public function down(Schema $schema) : void
    {
    }
}
