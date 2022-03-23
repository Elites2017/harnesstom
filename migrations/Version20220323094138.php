<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220323094138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accession ADD mls_status_id INT DEFAULT NULL, ADD breeding_info VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B075C9D2200F FOREIGN KEY (mls_status_id) REFERENCES mlsstatus (id)');
        $this->addSql('CREATE INDEX IDX_D983B075C9D2200F ON accession (mls_status_id)');
        $this->addSql('ALTER TABLE genetic_testing_model CHANGE description description VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accession DROP FOREIGN KEY FK_D983B075C9D2200F');
        $this->addSql('DROP INDEX IDX_D983B075C9D2200F ON accession');
        $this->addSql('ALTER TABLE accession DROP mls_status_id, DROP breeding_info');
        $this->addSql('ALTER TABLE genetic_testing_model CHANGE description description VARCHAR(255) DEFAULT NULL');
    }
}
