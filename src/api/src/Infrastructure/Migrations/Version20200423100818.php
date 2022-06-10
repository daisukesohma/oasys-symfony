<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200423100818 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $db = new TdbmFluidSchema($schema);
        $db->table('documents')
            ->column('to_be_signed')->boolean()->default(false)->graphqlField()
            ->column('status_signature')->string()->null()->graphqlField()
            ->column('procedure_id')->guid()->null();

        $db->table('documents_signers')
            ->column('id')->guid()->primaryKey()->comment('@UUID')
            ->column('document_id')->references('documents')->null()
            ->column('member_id')->guid()->null()->graphqlField()
            ->column('status_signature')->string()->null()->graphqlField()
            ->column('user_id')->references('users')->null()->graphqlField();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
