<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251108195749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD mot_de_passe VARCHAR(255) NOT NULL, ADD addresse VARCHAR(255) NOT NULL, ADD role VARCHAR(255) NOT NULL, DROP password, DROP address, DROP date_inscription');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD password VARCHAR(255) NOT NULL, ADD address VARCHAR(255) NOT NULL, ADD date_inscription VARCHAR(255) NOT NULL, DROP mot_de_passe, DROP addresse, DROP role');
    }
}
