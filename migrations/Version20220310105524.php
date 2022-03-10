<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220310105524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE germplasm_study_image (id INT AUTO_INCREMENT NOT NULL, factor_id INT DEFAULT NULL, development_stage_id INT DEFAULT NULL, plant_anatomical_entity_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_36B2A923BC88C1A3 (factor_id), INDEX IDX_36B2A923F7A0D6CD (development_stage_id), INDEX IDX_36B2A92391749822 (plant_anatomical_entity_id), INDEX IDX_36B2A923B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gwasvariant (id INT AUTO_INCREMENT NOT NULL, marker_id INT DEFAULT NULL, metabolite_id INT DEFAULT NULL, gwas_id INT DEFAULT NULL, trait_preprocessing_id INT DEFAULT NULL, observation_variable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, alternative_allele VARCHAR(255) NOT NULL, maf DOUBLE PRECISION NOT NULL, sample_size INT NOT NULL, snpp_value VARCHAR(255) NOT NULL, adjusted_pvalue DOUBLE PRECISION DEFAULT NULL, allelic_effect DOUBLE PRECISION NOT NULL, allelic_effect_stat DOUBLE PRECISION DEFAULT NULL, allelic_effectdf DOUBLE PRECISION DEFAULT NULL, allelic_eff_std_e DOUBLE PRECISION DEFAULT NULL, beta DOUBLE PRECISION DEFAULT NULL, beta_std_e DOUBLE PRECISION DEFAULT NULL, odds_ratio DOUBLE PRECISION DEFAULT NULL, ci_lower DOUBLE PRECISION DEFAULT NULL, ci_upper DOUBLE PRECISION DEFAULT NULL, r_square_of_mode DOUBLE PRECISION DEFAULT NULL, r_square_of_mode_with_snp DOUBLE PRECISION DEFAULT NULL, r_square_of_mode_without_snp DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, ref_allele VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, INDEX IDX_315CFF1474460EB (marker_id), INDEX IDX_315CFF179D97AF (metabolite_id), INDEX IDX_315CFF18182DDC1 (gwas_id), INDEX IDX_315CFF14F46B9BF (trait_preprocessing_id), INDEX IDX_315CFF1C05213BF (observation_variable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE observation_value (id INT AUTO_INCREMENT NOT NULL, observation_level_id INT DEFAULT NULL, observation_variable_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_A61E4A89DEAD8F70 (observation_level_id), INDEX IDX_A61E4A89C05213BF (observation_variable_id), INDEX IDX_A61E4A89B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pedigree (id INT AUTO_INCREMENT NOT NULL, pedigree_cross_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, pedigree_entry_id VARCHAR(255) DEFAULT NULL, generation VARCHAR(255) DEFAULT NULL, ancestor_pedigree_entry_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C36D0C7A8614D5A8 (pedigree_cross_id), INDEX IDX_C36D0C7AB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pedigree_germplasm (pedigree_id INT NOT NULL, germplasm_id INT NOT NULL, INDEX IDX_8363C20DA0917A9C (pedigree_id), INDEX IDX_8363C20D94DC88D9 (germplasm_id), PRIMARY KEY(pedigree_id, germplasm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sample (id INT AUTO_INCREMENT NOT NULL, study_id INT DEFAULT NULL, germplasm_id INT DEFAULT NULL, developmental_stage_id INT DEFAULT NULL, anatomical_entity_id INT DEFAULT NULL, observation_level_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, replicate VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, last_updated DATETIME DEFAULT NULL, INDEX IDX_F10B76C3E7B003E9 (study_id), INDEX IDX_F10B76C394DC88D9 (germplasm_id), INDEX IDX_F10B76C32405C8E (developmental_stage_id), INDEX IDX_F10B76C3FB41C074 (anatomical_entity_id), INDEX IDX_F10B76C3DEAD8F70 (observation_level_id), INDEX IDX_F10B76C3B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE germplasm_study_image ADD CONSTRAINT FK_36B2A923BC88C1A3 FOREIGN KEY (factor_id) REFERENCES factor_type (id)');
        $this->addSql('ALTER TABLE germplasm_study_image ADD CONSTRAINT FK_36B2A923F7A0D6CD FOREIGN KEY (development_stage_id) REFERENCES developmental_stage (id)');
        $this->addSql('ALTER TABLE germplasm_study_image ADD CONSTRAINT FK_36B2A92391749822 FOREIGN KEY (plant_anatomical_entity_id) REFERENCES anatomical_entity (id)');
        $this->addSql('ALTER TABLE germplasm_study_image ADD CONSTRAINT FK_36B2A923B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gwasvariant ADD CONSTRAINT FK_315CFF1474460EB FOREIGN KEY (marker_id) REFERENCES marker (id)');
        $this->addSql('ALTER TABLE gwasvariant ADD CONSTRAINT FK_315CFF179D97AF FOREIGN KEY (metabolite_id) REFERENCES metabolite (id)');
        $this->addSql('ALTER TABLE gwasvariant ADD CONSTRAINT FK_315CFF18182DDC1 FOREIGN KEY (gwas_id) REFERENCES gwas (id)');
        $this->addSql('ALTER TABLE gwasvariant ADD CONSTRAINT FK_315CFF14F46B9BF FOREIGN KEY (trait_preprocessing_id) REFERENCES trait_processing (id)');
        $this->addSql('ALTER TABLE gwasvariant ADD CONSTRAINT FK_315CFF1C05213BF FOREIGN KEY (observation_variable_id) REFERENCES observation_variable (id)');
        $this->addSql('ALTER TABLE observation_value ADD CONSTRAINT FK_A61E4A89DEAD8F70 FOREIGN KEY (observation_level_id) REFERENCES observation_level (id)');
        $this->addSql('ALTER TABLE observation_value ADD CONSTRAINT FK_A61E4A89C05213BF FOREIGN KEY (observation_variable_id) REFERENCES observation_variable (id)');
        $this->addSql('ALTER TABLE observation_value ADD CONSTRAINT FK_A61E4A89B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pedigree ADD CONSTRAINT FK_C36D0C7A8614D5A8 FOREIGN KEY (pedigree_cross_id) REFERENCES `cross` (id)');
        $this->addSql('ALTER TABLE pedigree ADD CONSTRAINT FK_C36D0C7AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pedigree_germplasm ADD CONSTRAINT FK_8363C20DA0917A9C FOREIGN KEY (pedigree_id) REFERENCES pedigree (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedigree_germplasm ADD CONSTRAINT FK_8363C20D94DC88D9 FOREIGN KEY (germplasm_id) REFERENCES germplasm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3E7B003E9 FOREIGN KEY (study_id) REFERENCES study (id)');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C394DC88D9 FOREIGN KEY (germplasm_id) REFERENCES germplasm (id)');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C32405C8E FOREIGN KEY (developmental_stage_id) REFERENCES developmental_stage (id)');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3FB41C074 FOREIGN KEY (anatomical_entity_id) REFERENCES anatomical_entity (id)');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3DEAD8F70 FOREIGN KEY (observation_level_id) REFERENCES observation_level (id)');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `cross` CHANGE parent1_id parent1_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE study_parameter_value MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE study_parameter_value DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE study_parameter_value ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedigree_germplasm DROP FOREIGN KEY FK_8363C20DA0917A9C');
        $this->addSql('DROP TABLE germplasm_study_image');
        $this->addSql('DROP TABLE gwasvariant');
        $this->addSql('DROP TABLE observation_value');
        $this->addSql('DROP TABLE pedigree');
        $this->addSql('DROP TABLE pedigree_germplasm');
        $this->addSql('DROP TABLE sample');
        $this->addSql('ALTER TABLE `cross` CHANGE parent1_id parent1_id INT NOT NULL');
        $this->addSql('ALTER TABLE study_parameter_value MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE study_parameter_value DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE study_parameter_value ADD PRIMARY KEY (id, parameter_id, study_id)');
    }
}
