<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251126234928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE id_user_id id_user_id INT DEFAULT NULL, CHANGE id_evenement_id id_evenement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evenement_ecologique CHANGE categorie categorie VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reclamation CHANGE id_user_id id_user_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE id_user_id id_user_id INT NOT NULL, CHANGE id_evenement_id id_evenement_id INT NOT NULL');
        $this->addSql('ALTER TABLE reclamation CHANGE id_user_id id_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE evenement_ecologique CHANGE categorie categorie LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
    }
}
