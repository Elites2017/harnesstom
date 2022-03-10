<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220310132634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mapping_population (id INT AUTO_INCREMENT NOT NULL, mapping_population_cross_id INT DEFAULT NULL, pedigree_generation_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, INDEX IDX_68D3FF92758F3022 (mapping_population_cross_id), INDEX IDX_68D3FF92D63ED958 (pedigree_generation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metabolite_value (id INT AUTO_INCREMENT NOT NULL, sample_id INT DEFAULT NULL, metabolite_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_66E8EA2D1B1FEA20 (sample_id), INDEX IDX_66E8EA2D79D97AF (metabolite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qtlstudy (id INT AUTO_INCREMENT NOT NULL, ci_criteria_id INT DEFAULT NULL, threshold_method_id INT DEFAULT NULL, software_id INT DEFAULT NULL, multi_environment_stat_id INT DEFAULT NULL, method_id INT DEFAULT NULL, variant_set_id INT DEFAULT NULL, mapping_population_id INT DEFAULT NULL, genome_map_unit_id INT DEFAULT NULL, statistic_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, qtl_count INT NOT NULL, threshold_value DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, publication_reference LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', study LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_4294152A14029D07 (ci_criteria_id), INDEX IDX_4294152AB349175C (threshold_method_id), INDEX IDX_4294152AD7452741 (software_id), INDEX IDX_4294152AFEA74501 (multi_environment_stat_id), INDEX IDX_4294152A19883967 (method_id), INDEX IDX_4294152A5A1AACE (variant_set_id), INDEX IDX_4294152A1428D7DA (mapping_population_id), INDEX IDX_4294152A33C52156 (genome_map_unit_id), INDEX IDX_4294152A53B6268F (statistic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE variant_set (id INT AUTO_INCREMENT NOT NULL, sample_id INT DEFAULT NULL, marker_id INT DEFAULT NULL, variant_set_metadata_id INT DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, INDEX IDX_CB929A9F1B1FEA20 (sample_id), INDEX IDX_CB929A9F474460EB (marker_id), INDEX IDX_CB929A9F581C2A5B (variant_set_metadata_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mapping_population ADD CONSTRAINT FK_68D3FF92758F3022 FOREIGN KEY (mapping_population_cross_id) REFERENCES `cross` (id)');
        $this->addSql('ALTER TABLE mapping_population ADD CONSTRAINT FK_68D3FF92D63ED958 FOREIGN KEY (pedigree_generation_id) REFERENCES pedigree (id)');
        $this->addSql('ALTER TABLE metabolite_value ADD CONSTRAINT FK_66E8EA2D1B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id)');
        $this->addSql('ALTER TABLE metabolite_value ADD CONSTRAINT FK_66E8EA2D79D97AF FOREIGN KEY (metabolite_id) REFERENCES metabolite (id)');
        $this->addSql('ALTER TABLE qtlstudy ADD CONSTRAINT FK_4294152A14029D07 FOREIGN KEY (ci_criteria_id) REFERENCES ci_criteria (id)');
        $this->addSql('ALTER TABLE qtlstudy ADD CONSTRAINT FK_4294152AB349175C FOREIGN KEY (threshold_method_id) REFERENCES threshold_method (id)');
        $this->addSql('ALTER TABLE qtlstudy ADD CONSTRAINT FK_4294152AD7452741 FOREIGN KEY (software_id) REFERENCES software (id)');
        $this->addSql('ALTER TABLE qtlstudy ADD CONSTRAINT FK_4294152AFEA74501 FOREIGN KEY (multi_environment_stat_id) REFERENCES qtlstatistic (id)');
        $this->addSql('ALTER TABLE qtlstudy ADD CONSTRAINT FK_4294152A19883967 FOREIGN KEY (method_id) REFERENCES qtlmethod (id)');
        $this->addSql('ALTER TABLE qtlstudy ADD CONSTRAINT FK_4294152A5A1AACE FOREIGN KEY (variant_set_id) REFERENCES variant_set (id)');
        $this->addSql('ALTER TABLE qtlstudy ADD CONSTRAINT FK_4294152A1428D7DA FOREIGN KEY (mapping_population_id) REFERENCES mapping_population (id)');
        $this->addSql('ALTER TABLE qtlstudy ADD CONSTRAINT FK_4294152A33C52156 FOREIGN KEY (genome_map_unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE qtlstudy ADD CONSTRAINT FK_4294152A53B6268F FOREIGN KEY (statistic_id) REFERENCES qtlstatistic (id)');
        $this->addSql('ALTER TABLE variant_set ADD CONSTRAINT FK_CB929A9F1B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id)');
        $this->addSql('ALTER TABLE variant_set ADD CONSTRAINT FK_CB929A9F474460EB FOREIGN KEY (marker_id) REFERENCES marker (id)');
        $this->addSql('ALTER TABLE variant_set ADD CONSTRAINT FK_CB929A9F581C2A5B FOREIGN KEY (variant_set_metadata_id) REFERENCES variant_set_metadata (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE qtlstudy DROP FOREIGN KEY FK_4294152A1428D7DA');
        $this->addSql('ALTER TABLE qtlstudy DROP FOREIGN KEY FK_4294152A5A1AACE');
        $this->addSql('DROP TABLE mapping_population');
        $this->addSql('DROP TABLE metabolite_value');
        $this->addSql('DROP TABLE qtlstudy');
        $this->addSql('DROP TABLE variant_set');
    }
}
