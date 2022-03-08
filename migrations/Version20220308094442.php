<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220308094442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metabolite (id INT AUTO_INCREMENT NOT NULL, analyte_id INT DEFAULT NULL, metabolic_trait_id INT DEFAULT NULL, scale_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_CA9F249AACDEDAAF (analyte_id), INDEX IDX_CA9F249AA85252AC (metabolic_trait_id), INDEX IDX_CA9F249AF73142C2 (scale_id), INDEX IDX_CA9F249AB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE program (id INT AUTO_INCREMENT NOT NULL, crop_id INT DEFAULT NULL, contact_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, abbreviation VARCHAR(255) NOT NULL, objective VARCHAR(255) DEFAULT NULL, external_ref VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_92ED7784888579EE (crop_id), INDEX IDX_92ED7784E7A1254A (contact_id), INDEX IDX_92ED7784B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metabolite ADD CONSTRAINT FK_CA9F249AACDEDAAF FOREIGN KEY (analyte_id) REFERENCES analyte (id)');
        $this->addSql('ALTER TABLE metabolite ADD CONSTRAINT FK_CA9F249AA85252AC FOREIGN KEY (metabolic_trait_id) REFERENCES metabolic_trait (id)');
        $this->addSql('ALTER TABLE metabolite ADD CONSTRAINT FK_CA9F249AF73142C2 FOREIGN KEY (scale_id) REFERENCES scale (id)');
        $this->addSql('ALTER TABLE metabolite ADD CONSTRAINT FK_CA9F249AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED7784888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED7784E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED7784B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX analyte_code ON analyte');
        $this->addSql('DROP INDEX orcid ON contact');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE metabolite');
        $this->addSql('DROP TABLE program');
        $this->addSql('CREATE UNIQUE INDEX analyte_code ON analyte (analyte_code)');
        $this->addSql('CREATE UNIQUE INDEX orcid ON contact (orcid)');
    }
}
