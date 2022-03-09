<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220309162720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE collection_class (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, publication_reference LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, germplasm_id LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_AAB3A1CCB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `cross` (id INT AUTO_INCREMENT NOT NULL, study_id INT DEFAULT NULL, institute_id INT DEFAULT NULL, breeding_method_id INT DEFAULT NULL, parent1_id INT NOT NULL, parent2_id INT NOT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, parent1_type VARCHAR(255) NOT NULL, parent2_type VARCHAR(255) NOT NULL, year INT DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, publication_reference LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_57131439E7B003E9 (study_id), INDEX IDX_57131439697B0F4C (institute_id), INDEX IDX_57131439142885F6 (breeding_method_id), INDEX IDX_57131439861B2665 (parent1_id), INDEX IDX_5713143994AE898B (parent2_id), INDEX IDX_57131439B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gwas (id INT AUTO_INCREMENT NOT NULL, variant_set_metada_id INT DEFAULT NULL, software_id INT DEFAULT NULL, gwas_model_id INT DEFAULT NULL, kinship_algorithm_id INT DEFAULT NULL, structure_method_id INT DEFAULT NULL, genetic_testing_model_id INT DEFAULT NULL, allelic_effect_estimator_id INT DEFAULT NULL, gwas_stat_test_id INT DEFAULT NULL, threshold_method_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, preprocessing VARCHAR(255) DEFAULT NULL, threshold_value DOUBLE PRECISION NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, study_id LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', publication_reference LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_63E11413FFB16BA8 (variant_set_metada_id), INDEX IDX_63E11413D7452741 (software_id), INDEX IDX_63E1141311BE63B5 (gwas_model_id), INDEX IDX_63E11413141103FA (kinship_algorithm_id), INDEX IDX_63E114138FD0944B (structure_method_id), INDEX IDX_63E11413A7F17EEC (genetic_testing_model_id), INDEX IDX_63E1141320ED8894 (allelic_effect_estimator_id), INDEX IDX_63E114137A44CEEA (gwas_stat_test_id), INDEX IDX_63E11413B349175C (threshold_method_id), INDEX IDX_63E11413B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE observation_level (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, germaplasm_id INT DEFAULT NULL, study_id INT DEFAULT NULL, unitname VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, block_number INT DEFAULT NULL, sub_block_number INT DEFAULT NULL, plot_number INT DEFAULT NULL, plant_number INT DEFAULT NULL, replicate INT DEFAULT NULL, unit_position INT DEFAULT NULL, unit_coordinate_x VARCHAR(255) DEFAULT NULL, unit_coordinate_y VARCHAR(255) DEFAULT NULL, unit_coordinate_xtype VARCHAR(255) DEFAULT NULL, unit_coordinate_ytype VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, last_updated DATETIME DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_2183DEAEB03A8386 (created_by_id), INDEX IDX_2183DEAEC89AB294 (germaplasm_id), INDEX IDX_2183DEAEE7B003E9 (study_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE study_image (id INT AUTO_INCREMENT NOT NULL, study_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_9A4754BFE7B003E9 (study_id), INDEX IDX_9A4754BFB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE study_parameter_value (id INT AUTO_INCREMENT NOT NULL, parameter_id INT NOT NULL, study_id INT NOT NULL, created_by_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_C00FFAB97C56DBD6 (parameter_id), UNIQUE INDEX UNIQ_C00FFAB9E7B003E9 (study_id), INDEX IDX_C00FFAB9B03A8386 (created_by_id), PRIMARY KEY(id, parameter_id, study_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE collection_class ADD CONSTRAINT FK_AAB3A1CCB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `cross` ADD CONSTRAINT FK_57131439E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id)');
        $this->addSql('ALTER TABLE `cross` ADD CONSTRAINT FK_57131439697B0F4C FOREIGN KEY (institute_id) REFERENCES institute (id)');
        $this->addSql('ALTER TABLE `cross` ADD CONSTRAINT FK_57131439142885F6 FOREIGN KEY (breeding_method_id) REFERENCES breeding_method (id)');
        $this->addSql('ALTER TABLE `cross` ADD CONSTRAINT FK_57131439861B2665 FOREIGN KEY (parent1_id) REFERENCES germplasm (id)');
        $this->addSql('ALTER TABLE `cross` ADD CONSTRAINT FK_5713143994AE898B FOREIGN KEY (parent2_id) REFERENCES germplasm (id)');
        $this->addSql('ALTER TABLE `cross` ADD CONSTRAINT FK_57131439B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gwas ADD CONSTRAINT FK_63E11413FFB16BA8 FOREIGN KEY (variant_set_metada_id) REFERENCES variant_set_metadata (id)');
        $this->addSql('ALTER TABLE gwas ADD CONSTRAINT FK_63E11413D7452741 FOREIGN KEY (software_id) REFERENCES software (id)');
        $this->addSql('ALTER TABLE gwas ADD CONSTRAINT FK_63E1141311BE63B5 FOREIGN KEY (gwas_model_id) REFERENCES gwasmodel (id)');
        $this->addSql('ALTER TABLE gwas ADD CONSTRAINT FK_63E11413141103FA FOREIGN KEY (kinship_algorithm_id) REFERENCES kinship_algorithm (id)');
        $this->addSql('ALTER TABLE gwas ADD CONSTRAINT FK_63E114138FD0944B FOREIGN KEY (structure_method_id) REFERENCES structure_method (id)');
        $this->addSql('ALTER TABLE gwas ADD CONSTRAINT FK_63E11413A7F17EEC FOREIGN KEY (genetic_testing_model_id) REFERENCES genetic_testing_model (id)');
        $this->addSql('ALTER TABLE gwas ADD CONSTRAINT FK_63E1141320ED8894 FOREIGN KEY (allelic_effect_estimator_id) REFERENCES allelic_effect_estimator (id)');
        $this->addSql('ALTER TABLE gwas ADD CONSTRAINT FK_63E114137A44CEEA FOREIGN KEY (gwas_stat_test_id) REFERENCES gwasstat_test (id)');
        $this->addSql('ALTER TABLE gwas ADD CONSTRAINT FK_63E11413B349175C FOREIGN KEY (threshold_method_id) REFERENCES threshold_method (id)');
        $this->addSql('ALTER TABLE gwas ADD CONSTRAINT FK_63E11413B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE observation_level ADD CONSTRAINT FK_2183DEAEB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE observation_level ADD CONSTRAINT FK_2183DEAEC89AB294 FOREIGN KEY (germaplasm_id) REFERENCES germplasm (id)');
        $this->addSql('ALTER TABLE observation_level ADD CONSTRAINT FK_2183DEAEE7B003E9 FOREIGN KEY (study_id) REFERENCES study (id)');
        $this->addSql('ALTER TABLE study_image ADD CONSTRAINT FK_9A4754BFE7B003E9 FOREIGN KEY (study_id) REFERENCES study (id)');
        $this->addSql('ALTER TABLE study_image ADD CONSTRAINT FK_9A4754BFB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE study_parameter_value ADD CONSTRAINT FK_C00FFAB97C56DBD6 FOREIGN KEY (parameter_id) REFERENCES parameter (id)');
        $this->addSql('ALTER TABLE study_parameter_value ADD CONSTRAINT FK_C00FFAB9E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id)');
        $this->addSql('ALTER TABLE study_parameter_value ADD CONSTRAINT FK_C00FFAB9B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX maintainer_numb ON germplasm');
        $this->addSql('DROP INDEX germplasm_id ON germplasm');
        $this->addSql('ALTER TABLE germplasm_study MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE germplasm_study DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE germplasm_study DROP id');
        $this->addSql('ALTER TABLE germplasm_study ADD PRIMARY KEY (germplasm_id, study_id)');
        $this->addSql('DROP INDEX abbreviation ON study');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE collection_class');
        $this->addSql('DROP TABLE `cross`');
        $this->addSql('DROP TABLE gwas');
        $this->addSql('DROP TABLE observation_level');
        $this->addSql('DROP TABLE study_image');
        $this->addSql('DROP TABLE study_parameter_value');
        $this->addSql('CREATE UNIQUE INDEX maintainer_numb ON germplasm (maintainer_numb)');
        $this->addSql('CREATE UNIQUE INDEX germplasm_id ON germplasm (germplasm_id)');
        $this->addSql('ALTER TABLE germplasm_study ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id, germplasm_id, study_id)');
        $this->addSql('CREATE UNIQUE INDEX abbreviation ON study (abbreviation)');
    }
}
