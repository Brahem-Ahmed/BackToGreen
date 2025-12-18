<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251213124500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove sentiment column from avis table (revert AI prototype)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE avis DROP sentiment');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE avis ADD sentiment VARCHAR(20) DEFAULT NULL');
    }
}
