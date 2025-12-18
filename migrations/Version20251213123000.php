<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251213123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add sentiment column to avis table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE avis ADD sentiment VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE avis DROP sentiment');
    }
}
