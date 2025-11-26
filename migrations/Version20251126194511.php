<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251126194511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE collecte_dechet (id INT AUTO_INCREMENT NOT NULL, id_zone_id INT DEFAULT NULL, id_user_id INT DEFAULT NULL, date_collecte DATETIME NOT NULL, quantite DOUBLE PRECISION NOT NULL, type_dechet VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_FE40281C41B196DB (id_zone_id), INDEX IDX_FE40281C79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement_ecologique (id INT AUTO_INCREMENT NOT NULL, id_organisateur_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_debut VARCHAR(255) NOT NULL, date_fin VARCHAR(255) NOT NULL, lieu VARCHAR(255) NOT NULL, capacite_max INT NOT NULL, categorie LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', INDEX IDX_AB3086B330687172 (id_organisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, id_evenement_id INT NOT NULL, id_user INT NOT NULL, date_inscription DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_AB55E24F2C115A61 (id_evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zone_collecte (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, capacite INT NOT NULL, horaires VARCHAR(255) NOT NULL, type_dechet VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE collecte_dechet ADD CONSTRAINT FK_FE40281C41B196DB FOREIGN KEY (id_zone_id) REFERENCES zone_collecte (id)');
        $this->addSql('ALTER TABLE collecte_dechet ADD CONSTRAINT FK_FE40281C79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE evenement_ecologique ADD CONSTRAINT FK_AB3086B330687172 FOREIGN KEY (id_organisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F2C115A61 FOREIGN KEY (id_evenement_id) REFERENCES evenement_ecologique (id)');
        $this->addSql('ALTER TABLE user CHANGE role role VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collecte_dechet DROP FOREIGN KEY FK_FE40281C41B196DB');
        $this->addSql('ALTER TABLE collecte_dechet DROP FOREIGN KEY FK_FE40281C79F37AE5');
        $this->addSql('ALTER TABLE evenement_ecologique DROP FOREIGN KEY FK_AB3086B330687172');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F2C115A61');
        $this->addSql('DROP TABLE collecte_dechet');
        $this->addSql('DROP TABLE evenement_ecologique');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE zone_collecte');
        $this->addSql('ALTER TABLE user CHANGE role role VARCHAR(255) DEFAULT NULL');
    }
}
