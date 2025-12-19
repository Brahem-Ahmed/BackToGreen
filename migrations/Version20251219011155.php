<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251219011155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE groupe_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, evenement_id INT NOT NULL, assigned_groupe_id INT DEFAULT NULL, processed_by_id INT DEFAULT NULL, statut VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', processed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FDF068D1A76ED395 (user_id), INDEX IDX_FDF068D1FD02F13 (evenement_id), INDEX IDX_FDF068D13E4802E (assigned_groupe_id), INDEX IDX_FDF068D12FFD4FD3 (processed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE groupe_request ADD CONSTRAINT FK_FDF068D1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE groupe_request ADD CONSTRAINT FK_FDF068D1FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement_ecologique (id)');
        $this->addSql('ALTER TABLE groupe_request ADD CONSTRAINT FK_FDF068D13E4802E FOREIGN KEY (assigned_groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE groupe_request ADD CONSTRAINT FK_FDF068D12FFD4FD3 FOREIGN KEY (processed_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe_request DROP FOREIGN KEY FK_FDF068D1A76ED395');
        $this->addSql('ALTER TABLE groupe_request DROP FOREIGN KEY FK_FDF068D1FD02F13');
        $this->addSql('ALTER TABLE groupe_request DROP FOREIGN KEY FK_FDF068D13E4802E');
        $this->addSql('ALTER TABLE groupe_request DROP FOREIGN KEY FK_FDF068D12FFD4FD3');
        $this->addSql('DROP TABLE groupe_request');
    }
}
