<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251011162124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fruit ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fruit ADD CONSTRAINT FK_A00BD297F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A00BD297F675F31B ON fruit (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE fruit DROP CONSTRAINT FK_A00BD297F675F31B');
        $this->addSql('DROP INDEX IDX_A00BD297F675F31B');
        $this->addSql('ALTER TABLE fruit DROP author_id');
    }
}
