<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211194544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement_ecologique DROP FOREIGN KEY FK_AB3086B330687172');
        $this->addSql('ALTER TABLE evenement_ecologique ADD groupe_id INT DEFAULT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE date_debut date_debut DATETIME DEFAULT NULL, CHANGE date_fin date_fin DATETIME DEFAULT NULL, CHANGE lieu lieu VARCHAR(255) DEFAULT NULL, CHANGE capacite_max capacite_max INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evenement_ecologique ADD CONSTRAINT FK_AB3086B37A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE evenement_ecologique ADD CONSTRAINT FK_AB3086B330687172 FOREIGN KEY (id_organisateur_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_AB3086B37A45358C ON evenement_ecologique (groupe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement_ecologique DROP FOREIGN KEY FK_AB3086B37A45358C');
        $this->addSql('ALTER TABLE evenement_ecologique DROP FOREIGN KEY FK_AB3086B330687172');
        $this->addSql('DROP INDEX IDX_AB3086B37A45358C ON evenement_ecologique');
        $this->addSql('ALTER TABLE evenement_ecologique DROP groupe_id, CHANGE description description VARCHAR(255) NOT NULL, CHANGE date_debut date_debut VARCHAR(255) NOT NULL, CHANGE date_fin date_fin VARCHAR(255) NOT NULL, CHANGE lieu lieu VARCHAR(255) NOT NULL, CHANGE capacite_max capacite_max INT NOT NULL');
        $this->addSql('ALTER TABLE evenement_ecologique ADD CONSTRAINT FK_AB3086B330687172 FOREIGN KEY (id_organisateur_id) REFERENCES user (id)');
    }
}
