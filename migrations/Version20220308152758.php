<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220308152758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attribute_trait_value (id INT AUTO_INCREMENT NOT NULL, trait_id INT DEFAULT NULL, metabolic_trait_id INT DEFAULT NULL, attribute_id INT DEFAULT NULL, accession_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, publication_reference LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_500AF9AC1C18632B (trait_id), INDEX IDX_500AF9ACA85252AC (metabolic_trait_id), INDEX IDX_500AF9ACB6E62EFA (attribute_id), INDEX IDX_500AF9ACA40F1370 (accession_id), INDEX IDX_500AF9ACB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE germplasm (id INT AUTO_INCREMENT NOT NULL, program_id INT DEFAULT NULL, accession_id INT NOT NULL, created_by_id INT DEFAULT NULL, germplasm_id VARCHAR(255) NOT NULL UNIQUE, preprocessing VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, instcode VARCHAR(255) NOT NULL, maintainer_numb VARCHAR(255) NOT NULL UNIQUE, INDEX IDX_7FBBB5293EB8070A (program_id), INDEX IDX_7FBBB529A40F1370 (accession_id), INDEX IDX_7FBBB529B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE germplasm_study (id INT AUTO_INCREMENT NOT NULL, germplasm_id INT NOT NULL, study_id INT NOT NULL, INDEX IDX_11D4EB994DC88D9 (germplasm_id), INDEX IDX_11D4EB9E7B003E9 (study_id), PRIMARY KEY(id, germplasm_id, study_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shared_with (id INT AUTO_INCREMENT NOT NULL, trial_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_226689965596D5F7 (trial_id), INDEX IDX_22668996A76ED395 (user_id), INDEX IDX_22668996B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE study (id INT AUTO_INCREMENT NOT NULL, trial_id INT DEFAULT NULL, factor_id INT DEFAULT NULL, season_id INT DEFAULT NULL, institute_id INT DEFAULT NULL, location_id INT DEFAULT NULL, growth_facility_id INT DEFAULT NULL, parameter_id INT DEFAULT NULL, experimental_design_type_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, abbreviation VARCHAR(255) NOT NULL UNIQUE, description VARCHAR(255) DEFAULT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, cultural_practice VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, last_updated DATETIME DEFAULT NULL, INDEX IDX_E67F97495596D5F7 (trial_id), INDEX IDX_E67F9749BC88C1A3 (factor_id), INDEX IDX_E67F97494EC001D1 (season_id), INDEX IDX_E67F9749697B0F4C (institute_id), INDEX IDX_E67F974964D218E (location_id), INDEX IDX_E67F9749DEDA642A (growth_facility_id), INDEX IDX_E67F97497C56DBD6 (parameter_id), INDEX IDX_E67F974914EF4924 (experimental_design_type_id), INDEX IDX_E67F9749B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE synonym (id INT AUTO_INCREMENT NOT NULL, accession_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, tgrc VARCHAR(255) DEFAULT NULL, usda VARCHAR(255) DEFAULT NULL, comav VARCHAR(255) DEFAULT NULL, fma01 VARCHAR(255) DEFAULT NULL, uib VARCHAR(255) DEFAULT NULL, pgr VARCHAR(255) DEFAULT NULL, eusol VARCHAR(255) DEFAULT NULL, cccode VARCHAR(255) DEFAULT NULL, ndl VARCHAR(255) DEFAULT NULL, avrc VARCHAR(255) DEFAULT NULL, inra VARCHAR(255) DEFAULT NULL, unitus VARCHAR(255) DEFAULT NULL, resq_project360 VARCHAR(255) DEFAULT NULL, reseq150 VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_A6315EC8A40F1370 (accession_id), INDEX IDX_A6315EC8B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attribute_trait_value ADD CONSTRAINT FK_500AF9AC1C18632B FOREIGN KEY (trait_id) REFERENCES trait_class (id)');
        $this->addSql('ALTER TABLE attribute_trait_value ADD CONSTRAINT FK_500AF9ACA85252AC FOREIGN KEY (metabolic_trait_id) REFERENCES metabolic_trait (id)');
        $this->addSql('ALTER TABLE attribute_trait_value ADD CONSTRAINT FK_500AF9ACB6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute (id)');
        $this->addSql('ALTER TABLE attribute_trait_value ADD CONSTRAINT FK_500AF9ACA40F1370 FOREIGN KEY (accession_id) REFERENCES accession (id)');
        $this->addSql('ALTER TABLE attribute_trait_value ADD CONSTRAINT FK_500AF9ACB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE germplasm ADD CONSTRAINT FK_7FBBB5293EB8070A FOREIGN KEY (program_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE germplasm ADD CONSTRAINT FK_7FBBB529A40F1370 FOREIGN KEY (accession_id) REFERENCES accession (id)');
        $this->addSql('ALTER TABLE germplasm ADD CONSTRAINT FK_7FBBB529B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE germplasm_study ADD CONSTRAINT FK_11D4EB994DC88D9 FOREIGN KEY (germplasm_id) REFERENCES germplasm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE germplasm_study ADD CONSTRAINT FK_11D4EB9E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shared_with ADD CONSTRAINT FK_226689965596D5F7 FOREIGN KEY (trial_id) REFERENCES trial (id)');
        $this->addSql('ALTER TABLE shared_with ADD CONSTRAINT FK_22668996A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE shared_with ADD CONSTRAINT FK_22668996B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F97495596D5F7 FOREIGN KEY (trial_id) REFERENCES trial (id)');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F9749BC88C1A3 FOREIGN KEY (factor_id) REFERENCES factor_type (id)');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F97494EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F9749697B0F4C FOREIGN KEY (institute_id) REFERENCES institute (id)');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F974964D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F9749DEDA642A FOREIGN KEY (growth_facility_id) REFERENCES growth_facility_type (id)');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F97497C56DBD6 FOREIGN KEY (parameter_id) REFERENCES parameter (id)');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F974914EF4924 FOREIGN KEY (experimental_design_type_id) REFERENCES experimental_design_type (id)');
        $this->addSql('ALTER TABLE study ADD CONSTRAINT FK_E67F9749B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE synonym ADD CONSTRAINT FK_A6315EC8A40F1370 FOREIGN KEY (accession_id) REFERENCES accession (id)');
        $this->addSql('ALTER TABLE synonym ADD CONSTRAINT FK_A6315EC8B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX maintainernumb ON accession');
        $this->addSql('DROP INDEX donornumb ON accession');
        $this->addSql('DROP INDEX accenumb ON accession');
        $this->addSql('DROP INDEX puid ON accession');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE germplasm_study DROP FOREIGN KEY FK_11D4EB994DC88D9');
        $this->addSql('ALTER TABLE germplasm_study DROP FOREIGN KEY FK_11D4EB9E7B003E9');
        $this->addSql('DROP TABLE attribute_trait_value');
        $this->addSql('DROP TABLE germplasm');
        $this->addSql('DROP TABLE germplasm_study');
        $this->addSql('DROP TABLE shared_with');
        $this->addSql('DROP TABLE study');
        $this->addSql('DROP TABLE synonym');
        $this->addSql('CREATE UNIQUE INDEX maintainernumb ON accession (maintainernumb)');
        $this->addSql('CREATE UNIQUE INDEX donornumb ON accession (donornumb)');
        $this->addSql('CREATE UNIQUE INDEX accenumb ON accession (accenumb)');
        $this->addSql('CREATE UNIQUE INDEX puid ON accession (puid)');
    }
}
