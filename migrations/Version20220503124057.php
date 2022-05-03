<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220503124057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE study_gwas');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE study_gwas (study_id INT NOT NULL, gwas_id INT NOT NULL, INDEX IDX_1C39FC2A8182DDC1 (gwas_id), INDEX IDX_1C39FC2AE7B003E9 (study_id), PRIMARY KEY(study_id, gwas_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE study_gwas ADD CONSTRAINT FK_1C39FC2A8182DDC1 FOREIGN KEY (gwas_id) REFERENCES gwas (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE study_gwas ADD CONSTRAINT FK_1C39FC2AE7B003E9 FOREIGN KEY (study_id) REFERENCES study (id) ON DELETE CASCADE');
    }
}
