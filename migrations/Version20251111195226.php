<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251111195226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE groupe (id INT AUTO_INCREMENT NOT NULL, id_createur_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, nombre_membres INT NOT NULL, INDEX IDX_4B98C216BB0CC12 (id_createur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membre_groupe (id INT AUTO_INCREMENT NOT NULL, id_groupe_id INT DEFAULT NULL, id_user_id INT DEFAULT NULL, date_adhesion DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_9EB01998FA7089AB (id_groupe_id), INDEX IDX_9EB0199879F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C216BB0CC12 FOREIGN KEY (id_createur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB01998FA7089AB FOREIGN KEY (id_groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB0199879F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE evenement_ecologique DROP FOREIGN KEY FK_AB3086B330687172');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F2C115A61');
        $this->addSql('DROP TABLE collecte_dechet');
        $this->addSql('DROP TABLE evenement_ecologique');
        $this->addSql('DROP TABLE participation');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE collecte_dechet (id INT AUTO_INCREMENT NOT NULL, id_zone_id INT DEFAULT NULL, id_user_id INT DEFAULT NULL, date_collecte DATETIME NOT NULL, quantite DOUBLE PRECISION NOT NULL, type_dechet VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, statut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_FE40281C41B196DB (id_zone_id), INDEX IDX_FE40281C79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evenement_ecologique (id INT AUTO_INCREMENT NOT NULL, id_organisateur_id INT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_debut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_fin VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, lieu VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, capacite_max INT NOT NULL, categorie LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:simple_array)\', INDEX IDX_AB3086B330687172 (id_organisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, id_evenement_id INT NOT NULL, id_user INT NOT NULL, date_inscription DATETIME NOT NULL, statut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_AB55E24F2C115A61 (id_evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE evenement_ecologique ADD CONSTRAINT FK_AB3086B330687172 FOREIGN KEY (id_organisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F2C115A61 FOREIGN KEY (id_evenement_id) REFERENCES evenement_ecologique (id)');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C216BB0CC12');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB01998FA7089AB');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB0199879F37AE5');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE membre_groupe');s
    }
}
