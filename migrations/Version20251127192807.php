<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251127192807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // remove an existing CHECK/enum constraint that can block changing the column type
        // some MySQL/MariaDB installations create a constraint name matching `categorie`
        // try dropping it first (available MySQL 8.0.16+, MariaDB). If your server
        // doesn't support DROP CHECK or the constraint has a different name, you
        // may need to adapt this migration.
        // Safer approach for MariaDB / MySQL: create a new JSON column, copy/normalize
        // values from the existing `categorie` column, then drop the old column
        // and rename the new one. This avoids errors from CHECK/enum constraints.

        // 1) add new temporary JSON column
        $this->addSql('ALTER TABLE evenement_ecologique ADD COLUMN categorie_json JSON DEFAULT NULL');

        // 2) copy values: if the existing value looks like a simple enum (no leading 'a:'),
        // wrap it in a JSON array. For serialized php arrays or complex values we set an empty array.
        // Note: SQL-level detection is heuristic — we treat values starting with 'a:' as serialized.
        $this->addSql("UPDATE evenement_ecologique SET categorie_json = JSON_ARRAY(categorie) WHERE categorie IS NOT NULL AND categorie <> '' AND categorie NOT LIKE 'a:%'");
        $this->addSql("UPDATE evenement_ecologique SET categorie_json = JSON_ARRAY() WHERE categorie IS NULL OR categorie = '' OR categorie LIKE 'a:%'");

        // 3) drop old column (this removes any blocking constraint tied to it)
        $this->addSql('ALTER TABLE evenement_ecologique DROP COLUMN categorie');

        // 4) rename temporary column to original name and add doctrine json comment
        $this->addSql('ALTER TABLE evenement_ecologique CHANGE categorie_json categorie JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // Reverse: create a varchar column, extract first JSON element into it, drop JSON column
        $this->addSql('ALTER TABLE evenement_ecologique ADD COLUMN categorie_old VARCHAR(255) DEFAULT NULL');
        $this->addSql("UPDATE evenement_ecologique SET categorie_old = JSON_UNQUOTE(JSON_EXTRACT(categorie, '$[0]')) WHERE categorie IS NOT NULL");
        $this->addSql('ALTER TABLE evenement_ecologique DROP COLUMN categorie');
        $this->addSql('ALTER TABLE evenement_ecologique CHANGE categorie_old categorie VARCHAR(255) NOT NULL');
    }
}
