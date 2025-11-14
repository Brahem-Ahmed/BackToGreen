<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251113233752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_evenement_id INT NOT NULL, commentaire VARCHAR(255) NOT NULL, note INT NOT NULL, date_avis DATETIME NOT NULL, INDEX IDX_8F91ABF079F37AE5 (id_user_id), INDEX IDX_8F91ABF02C115A61 (id_evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, description VARCHAR(255) NOT NULL, date_reclamation DATETIME NOT NULL, reponse VARCHAR(255) NOT NULL, priorite VARCHAR(255) DEFAULT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_CE60640479F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zone_collecte (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, capacite INT NOT NULL, horaires VARCHAR(255) NOT NULL, type_dechet VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF079F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF02C115A61 FOREIGN KEY (id_evenement_id) REFERENCES evenement_ecologique (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640479F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE collecte_dechet ADD CONSTRAINT FK_FE40281C41B196DB FOREIGN KEY (id_zone_id) REFERENCES zone_collecte (id)');
        $this->addSql('ALTER TABLE collecte_dechet ADD CONSTRAINT FK_FE40281C79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collecte_dechet DROP FOREIGN KEY FK_FE40281C41B196DB');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF079F37AE5');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF02C115A61');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE60640479F37AE5');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE zone_collecte');
        $this->addSql('ALTER TABLE collecte_dechet DROP FOREIGN KEY FK_FE40281C79F37AE5');
    }
}
