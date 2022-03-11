<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220307122801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE analyte (id INT AUTO_INCREMENT NOT NULL, annotation_level_id INT DEFAULT NULL, identification_level_id INT DEFAULT NULL, observation_variable_method_id INT DEFAULT NULL, analyte_class_id INT DEFAULT NULL, health_and_flavor_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, analyte_code VARCHAR(255) NOT NULL UNIQUE, retention_time DOUBLE PRECISION DEFAULT NULL, mass_to_charge_ratio DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_7A5A9AB9D4D491B8 (annotation_level_id), INDEX IDX_7A5A9AB9801BBC40 (identification_level_id), INDEX IDX_7A5A9AB9A16D56D8 (observation_variable_method_id), INDEX IDX_7A5A9AB9B35B08D1 (analyte_class_id), INDEX IDX_7A5A9AB950D01697 (health_and_flavor_id), INDEX IDX_7A5A9AB9B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE analyte_class (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_3CD68A4FB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE analyte_flavor_health (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_EC4D5410B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collecting_mission (id INT AUTO_INCREMENT NOT NULL, institute_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, species VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_872A5D88697B0F4C (institute_id), INDEX IDX_872A5D88B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, institute_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, orcid VARCHAR(255) NOT NULL UNIQUE, type VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_4C62E638217BBB47 (person_id), INDEX IDX_4C62E638697B0F4C (institute_id), INDEX IDX_4C62E638B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enzyme (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_7DD0657CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genotyping_platform (id INT AUTO_INCREMENT NOT NULL, sequencing_type_id INT DEFAULT NULL, sequencing_instrument_id INT DEFAULT NULL, var_call_software_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, method_description VARCHAR(255) DEFAULT NULL, ref_set_name VARCHAR(255) DEFAULT NULL, published_date DATE DEFAULT NULL, bio_project_id VARCHAR(255) DEFAULT NULL, marker_count INT DEFAULT NULL, assembly_pui VARCHAR(255) NOT NULL, publication_ref LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_7AF5CE6F2C5F308C (sequencing_type_id), INDEX IDX_7AF5CE6FCFD95B99 (sequencing_instrument_id), INDEX IDX_7AF5CE6F9373FE9C (var_call_software_id), INDEX IDX_7AF5CE6FB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE marker (id INT AUTO_INCREMENT NOT NULL, genotyping_platform_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, linkage_group_name VARCHAR(255) NOT NULL, position INT NOT NULL, start INT DEFAULT NULL, end INT DEFAULT NULL, ref_allele VARCHAR(255) DEFAULT NULL, alt_allele LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', primer_name1 VARCHAR(255) DEFAULT NULL, primer_seq1 VARCHAR(255) DEFAULT NULL, primer_name2 VARCHAR(255) DEFAULT NULL, primer_seq2 VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_82CF20FEBD64B504 (genotyping_platform_id), INDEX IDX_82CF20FEB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE observation_variable (id INT AUTO_INCREMENT NOT NULL, trait_id INT DEFAULT NULL, scale_id INT DEFAULT NULL, observation_variable_method_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, main_abbreviaition VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_4D51435A1C18632B (trait_id), INDEX IDX_4D51435AF73142C2 (scale_id), INDEX IDX_4D51435AA16D56D8 (observation_variable_method_id), INDEX IDX_4D51435AB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE variant_set_metadata (id INT AUTO_INCREMENT NOT NULL, genotyping_platform_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, filters VARCHAR(255) DEFAULT NULL, variant_count INT DEFAULT NULL, publication_ref LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', data_upload VARCHAR(255) NOT NULL, file_url VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_C9C0DDC0BD64B504 (genotyping_platform_id), INDEX IDX_C9C0DDC0B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE analyte ADD CONSTRAINT FK_7A5A9AB9D4D491B8 FOREIGN KEY (annotation_level_id) REFERENCES annotation_level (id)');
        $this->addSql('ALTER TABLE analyte ADD CONSTRAINT FK_7A5A9AB9801BBC40 FOREIGN KEY (identification_level_id) REFERENCES identification_level (id)');
        $this->addSql('ALTER TABLE analyte ADD CONSTRAINT FK_7A5A9AB9A16D56D8 FOREIGN KEY (observation_variable_method_id) REFERENCES observation_variable_method (id)');
        $this->addSql('ALTER TABLE analyte ADD CONSTRAINT FK_7A5A9AB9B35B08D1 FOREIGN KEY (analyte_class_id) REFERENCES analyte_class (id)');
        $this->addSql('ALTER TABLE analyte ADD CONSTRAINT FK_7A5A9AB950D01697 FOREIGN KEY (health_and_flavor_id) REFERENCES analyte_flavor_health (id)');
        $this->addSql('ALTER TABLE analyte ADD CONSTRAINT FK_7A5A9AB9B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE analyte_class ADD CONSTRAINT FK_3CD68A4FB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE analyte_flavor_health ADD CONSTRAINT FK_EC4D5410B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE collecting_mission ADD CONSTRAINT FK_872A5D88697B0F4C FOREIGN KEY (institute_id) REFERENCES institute (id)');
        $this->addSql('ALTER TABLE collecting_mission ADD CONSTRAINT FK_872A5D88B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638697B0F4C FOREIGN KEY (institute_id) REFERENCES institute (id)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE enzyme ADD CONSTRAINT FK_7DD0657CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE genotyping_platform ADD CONSTRAINT FK_7AF5CE6F2C5F308C FOREIGN KEY (sequencing_type_id) REFERENCES sequencing_type (id)');
        $this->addSql('ALTER TABLE genotyping_platform ADD CONSTRAINT FK_7AF5CE6FCFD95B99 FOREIGN KEY (sequencing_instrument_id) REFERENCES sequencing_instrument (id)');
        $this->addSql('ALTER TABLE genotyping_platform ADD CONSTRAINT FK_7AF5CE6F9373FE9C FOREIGN KEY (var_call_software_id) REFERENCES var_call_software (id)');
        $this->addSql('ALTER TABLE genotyping_platform ADD CONSTRAINT FK_7AF5CE6FB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE marker ADD CONSTRAINT FK_82CF20FEBD64B504 FOREIGN KEY (genotyping_platform_id) REFERENCES genotyping_platform (id)');
        $this->addSql('ALTER TABLE marker ADD CONSTRAINT FK_82CF20FEB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE observation_variable ADD CONSTRAINT FK_4D51435A1C18632B FOREIGN KEY (trait_id) REFERENCES trait_class (id)');
        $this->addSql('ALTER TABLE observation_variable ADD CONSTRAINT FK_4D51435AF73142C2 FOREIGN KEY (scale_id) REFERENCES scale (id)');
        $this->addSql('ALTER TABLE observation_variable ADD CONSTRAINT FK_4D51435AA16D56D8 FOREIGN KEY (observation_variable_method_id) REFERENCES observation_variable_method (id)');
        $this->addSql('ALTER TABLE observation_variable ADD CONSTRAINT FK_4D51435AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE variant_set_metadata ADD CONSTRAINT FK_C9C0DDC0BD64B504 FOREIGN KEY (genotyping_platform_id) REFERENCES genotyping_platform (id)');
        $this->addSql('ALTER TABLE variant_set_metadata ADD CONSTRAINT FK_C9C0DDC0B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX instcode ON institute');
        $this->addSql('DROP INDEX acronym ON institute');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE analyte DROP FOREIGN KEY FK_7A5A9AB9B35B08D1');
        $this->addSql('ALTER TABLE analyte DROP FOREIGN KEY FK_7A5A9AB950D01697');
        $this->addSql('ALTER TABLE marker DROP FOREIGN KEY FK_82CF20FEBD64B504');
        $this->addSql('ALTER TABLE variant_set_metadata DROP FOREIGN KEY FK_C9C0DDC0BD64B504');
        $this->addSql('DROP TABLE analyte');
        $this->addSql('DROP TABLE analyte_class');
        $this->addSql('DROP TABLE analyte_flavor_health');
        $this->addSql('DROP TABLE collecting_mission');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE enzyme');
        $this->addSql('DROP TABLE genotyping_platform');
        $this->addSql('DROP TABLE marker');
        $this->addSql('DROP TABLE observation_variable');
        $this->addSql('DROP TABLE variant_set_metadata');
        $this->addSql('CREATE UNIQUE INDEX instcode ON institute (instcode)');
        $this->addSql('CREATE UNIQUE INDEX acronym ON institute (acronym)');
    }
}
