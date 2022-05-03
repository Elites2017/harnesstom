<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220503093144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE study ADD g_was_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F9749386E9F41 FOREIGN KEY (g_was_id) REFERENCES gwas (id)');
        $this->addSql('CREATE INDEX IDX_E67F9749386E9F41 ON study (g_was_id)');
        $this->addSql('ALTER TABLE gwas ADD studList LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE study DROP FOREIGN KEY FK_E67F9749386E9F41');
        $this->addSql('DROP INDEX IDX_E67F9749386E9F41 ON study');
        $this->addSql('ALTER TABLE study DROP g_was_id');
        $this->addSql('ALTER TABLE gwas DROP studList');
    }
}
