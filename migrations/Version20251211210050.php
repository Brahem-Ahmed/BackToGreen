<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211210050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE membre_groupe ADD id_evenement INT DEFAULT NULL');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB019988B13D439 FOREIGN KEY (id_evenement) REFERENCES evenement_ecologique (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_9EB019988B13D439 ON membre_groupe (id_evenement)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB019988B13D439');
        $this->addSql('DROP INDEX IDX_9EB019988B13D439 ON membre_groupe');
        $this->addSql('ALTER TABLE membre_groupe DROP id_evenement');
    }
}
