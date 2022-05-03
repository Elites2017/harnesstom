<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220503125312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gwas_study (gwas_id INT NOT NULL, study_id INT NOT NULL, INDEX IDX_583769868182DDC1 (gwas_id), INDEX IDX_58376986E7B003E9 (study_id), PRIMARY KEY(gwas_id, study_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gwas_study ADD CONSTRAINT FK_583769868182DDC1 FOREIGN KEY (gwas_id) REFERENCES gwas (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gwas_study ADD CONSTRAINT FK_58376986E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE gwas_study');
    }
}
