<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220310145629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE qtlvariant (id INT AUTO_INCREMENT NOT NULL, qtl_study_id INT DEFAULT NULL, observation_variable_id INT DEFAULT NULL, metabolite_id INT DEFAULT NULL, closest_marker_id INT DEFAULT NULL, flanking_marker_start_id INT DEFAULT NULL, flanking_marker_end_id INT DEFAULT NULL, positive_allele_parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, publication_reference LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', locus_name VARCHAR(255) DEFAULT NULL, locus VARCHAR(255) DEFAULT NULL, r2_qtlx_e DOUBLE PRECISION DEFAULT NULL, r2_global DOUBLE PRECISION DEFAULT NULL, statistic_qtlx_evalue DOUBLE PRECISION DEFAULT NULL, r2 DOUBLE PRECISION DEFAULT NULL, d_a DOUBLE PRECISION DEFAULT NULL, dominance DOUBLE PRECISION DEFAULT NULL, additive DOUBLE PRECISION DEFAULT NULL, qtl_stats_value DOUBLE PRECISION DEFAULT NULL, positive_allele VARCHAR(255) DEFAULT NULL, ci_start INT DEFAULT NULL, ci_end INT DEFAULT NULL, detect_name VARCHAR(255) DEFAULT NULL, original_trait_name VARCHAR(255) DEFAULT NULL, peak_position INT DEFAULT NULL, linkage_group_name VARCHAR(255) DEFAULT NULL, INDEX IDX_BC25C12E6E639C5C (qtl_study_id), INDEX IDX_BC25C12EC05213BF (observation_variable_id), INDEX IDX_BC25C12E79D97AF (metabolite_id), INDEX IDX_BC25C12E10EA0120 (closest_marker_id), INDEX IDX_BC25C12E3FF9774A (flanking_marker_start_id), INDEX IDX_BC25C12ECB97303B (flanking_marker_end_id), INDEX IDX_BC25C12E6F5D9A36 (positive_allele_parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE qtlvariant ADD CONSTRAINT FK_BC25C12E6E639C5C FOREIGN KEY (qtl_study_id) REFERENCES qtlstudy (id)');
        $this->addSql('ALTER TABLE qtlvariant ADD CONSTRAINT FK_BC25C12EC05213BF FOREIGN KEY (observation_variable_id) REFERENCES observation_variable (id)');
        $this->addSql('ALTER TABLE qtlvariant ADD CONSTRAINT FK_BC25C12E79D97AF FOREIGN KEY (metabolite_id) REFERENCES metabolite (id)');
        $this->addSql('ALTER TABLE qtlvariant ADD CONSTRAINT FK_BC25C12E10EA0120 FOREIGN KEY (closest_marker_id) REFERENCES marker (id)');
        $this->addSql('ALTER TABLE qtlvariant ADD CONSTRAINT FK_BC25C12E3FF9774A FOREIGN KEY (flanking_marker_start_id) REFERENCES marker (id)');
        $this->addSql('ALTER TABLE qtlvariant ADD CONSTRAINT FK_BC25C12ECB97303B FOREIGN KEY (flanking_marker_end_id) REFERENCES marker (id)');
        $this->addSql('ALTER TABLE qtlvariant ADD CONSTRAINT FK_BC25C12E6F5D9A36 FOREIGN KEY (positive_allele_parent_id) REFERENCES germplasm (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE qtlvariant');
    }
}
