<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use App\Domain\Enum\ProfessionalCategoryEnum;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201117120632 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create appointment time limit and link ID columns';
    }

    public function up(Schema $schema) : void
    {
       $db = new TdbmFluidSchema($schema);
       $db->table('program_pic')
          ->column('appointment_time_limit')->integer()->default(0)->graphqlField()
          ->column('link_id')->string()->null()->graphqlField();

        $db->table('professional_categories')
           ->column('id')->string(50)->primaryKey()->graphqlField()
           ->column('label')->string(50)->notNull()->graphqlField();

        $valueMap = array_flip(ProfessionalCategoryEnum::values());
        $statement = $this->connection->query('
            SELECT id, professional_category
            FROM users
        ');

        foreach ($statement->fetchAll(FetchMode::ASSOCIATIVE) as $row) {
            if (empty($row['professional_category'])) {
                continue;
            }

            $this->connection->update('users', [
                'professional_category' => $valueMap[$row['professional_category']],
            ], [
                'id' => $row['id'],
            ]);
        }
    }

    public function postUp(Schema $schema): void
    {
        parent::postUp($schema);

        foreach (ProfessionalCategoryEnum::values() as $id => $label) {
            $this->connection->insert('professional_categories', [
                'id' => $id,
                'label' => $label,
            ]);
        }

        $this->connection->exec('
            ALTER TABLE users
            ADD CONSTRAINT FK_PCUSERS
            FOREIGN KEY (professional_category) REFERENCES professional_categories (id);
        ');
    }

    public function down(Schema $schema) : void
    {
    }
}
