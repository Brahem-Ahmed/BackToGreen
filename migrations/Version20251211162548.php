<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211162548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement_ecologique CHANGE id_organisateur_id id_organisateur_id INT DEFAULT NULL, CHANGE categorie categorie VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB01998228E39CC FOREIGN KEY (id_groupe) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB019986B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9EB01998228E39CC ON membre_groupe (id_groupe)');
        $this->addSql('CREATE INDEX IDX_9EB019986B3CA4B ON membre_groupe (id_user)');
        $this->addSql('CREATE UNIQUE INDEX unique_user_groupe ON membre_groupe (id_user, id_groupe)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement_ecologique CHANGE id_organisateur_id id_organisateur_id INT NOT NULL, CHANGE categorie categorie VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB01998228E39CC');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB019986B3CA4B');
        $this->addSql('DROP INDEX IDX_9EB01998228E39CC ON membre_groupe');
        $this->addSql('DROP INDEX IDX_9EB019986B3CA4B ON membre_groupe');
        $this->addSql('DROP INDEX unique_user_groupe ON membre_groupe');
    }
}
