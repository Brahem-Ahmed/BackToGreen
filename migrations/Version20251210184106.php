<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251210184106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Temporarily disable foreign key checks to avoid constraint violations during migration
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Add new column for event assignment on groupe (only if it doesn't exist)
        $this->addSql('ALTER TABLE groupe ADD id_evenement INT DEFAULT NULL');

        // Clean up orphaned membre_groupe rows before adding foreign keys
        // Delete rows where id_groupe references a non-existent groupe
        $this->addSql('DELETE FROM membre_groupe WHERE id_groupe NOT IN (SELECT id FROM groupe) AND id_groupe IS NOT NULL');
        // Delete rows where id_user references a non-existent user
        $this->addSql('DELETE FROM membre_groupe WHERE id_user NOT IN (SELECT id FROM `user`) AND id_user IS NOT NULL');

        // Drop existing constraints if they exist (safe cleanup)
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY IF EXISTS FK_9EB01998228E39CC');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY IF EXISTS FK_9EB019986B3CA4B');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY IF EXISTS FK_4B98C218B13D439');

        // Drop existing indexes if they exist
        $this->addSql('DROP INDEX IF EXISTS IDX_9EB01998228E39CC ON membre_groupe');
        $this->addSql('DROP INDEX IF EXISTS IDX_9EB019986B3CA4B ON membre_groupe');
        $this->addSql('DROP INDEX IF EXISTS IDX_4B98C218B13D439 ON groupe');
        $this->addSql('DROP INDEX IF EXISTS unique_user_groupe ON membre_groupe');

        // Create indexes
        $this->addSql('CREATE INDEX IDX_4B98C218B13D439 ON groupe (id_evenement)');
        $this->addSql('CREATE INDEX IDX_9EB01998228E39CC ON membre_groupe (id_groupe)');
        $this->addSql('CREATE INDEX IDX_9EB019986B3CA4B ON membre_groupe (id_user)');

        // Add foreign keys on membre_groupe
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB01998228E39CC FOREIGN KEY (id_groupe) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB019986B3CA4B FOREIGN KEY (id_user) REFERENCES `user` (id)');

        // Add foreign key for groupe to evenement_ecologique
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C218B13D439 FOREIGN KEY (id_evenement) REFERENCES evenement_ecologique (id)');

        // Create unique index to prevent duplicate memberships
        $this->addSql('CREATE UNIQUE INDEX unique_user_groupe ON membre_groupe (id_user, id_groupe)');

        // Re-enable foreign key checks
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C218B13D439');
        $this->addSql('DROP INDEX IDX_4B98C218B13D439 ON groupe');
        $this->addSql('ALTER TABLE groupe DROP id_evenement');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB01998228E39CC');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB019986B3CA4B');
        $this->addSql('DROP INDEX IDX_9EB01998228E39CC ON membre_groupe');
        $this->addSql('DROP INDEX IDX_9EB019986B3CA4B ON membre_groupe');
        $this->addSql('DROP INDEX unique_user_groupe ON membre_groupe');
    }
}
