<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220310113658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mapping_population (id INT AUTO_INCREMENT NOT NULL, mapping_population_cross_id INT DEFAULT NULL, pedigree_generation_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, INDEX IDX_68D3FF92758F3022 (mapping_population_cross_id), INDEX IDX_68D3FF92D63ED958 (pedigree_generation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metabolite_value (id INT AUTO_INCREMENT NOT NULL, sample_id INT DEFAULT NULL, metabolite_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_66E8EA2D1B1FEA20 (sample_id), INDEX IDX_66E8EA2D79D97AF (metabolite_id), PRIMARY KEY(id, metabolite_id, sample_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE variant_set (id INT AUTO_INCREMENT NOT NULL UNIQUE, sample_id INT DEFAULT NULL, marker_id INT DEFAULT NULL, variant_set_metadata_id INT DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, INDEX IDX_CB929A9F1B1FEA20 (sample_id), INDEX IDX_CB929A9F474460EB (marker_id), INDEX IDX_CB929A9F581C2A5B (variant_set_metadata_id), PRIMARY KEY(sample_id, marker_id, variant_set_metadata_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mapping_population ADD CONSTRAINT FK_68D3FF92758F3022 FOREIGN KEY (mapping_population_cross_id) REFERENCES `cross` (id)');
        $this->addSql('ALTER TABLE mapping_population ADD CONSTRAINT FK_68D3FF92D63ED958 FOREIGN KEY (pedigree_generation_id) REFERENCES pedigree (id)');
        $this->addSql('ALTER TABLE metabolite_value ADD CONSTRAINT FK_66E8EA2D1B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id)');
        $this->addSql('ALTER TABLE metabolite_value ADD CONSTRAINT FK_66E8EA2D79D97AF FOREIGN KEY (metabolite_id) REFERENCES metabolite (id)');
        $this->addSql('ALTER TABLE variant_set ADD CONSTRAINT FK_CB929A9F1B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id)');
        $this->addSql('ALTER TABLE variant_set ADD CONSTRAINT FK_CB929A9F474460EB FOREIGN KEY (marker_id) REFERENCES marker (id)');
        $this->addSql('ALTER TABLE variant_set ADD CONSTRAINT FK_CB929A9F581C2A5B FOREIGN KEY (variant_set_metadata_id) REFERENCES variant_set_metadata (id)');
        $this->addSql('ALTER TABLE observation_value MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE observation_value DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE observation_value CHANGE observation_level_id observation_level_id INT DEFAULT NULL, CHANGE observation_variable_id observation_variable_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE observation_value ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE study_parameter_value CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE mapping_population');
        $this->addSql('DROP TABLE metabolite_value');
        $this->addSql('DROP TABLE variant_set');
        $this->addSql('ALTER TABLE observation_value MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE observation_value DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE observation_value CHANGE observation_level_id observation_level_id INT NOT NULL, CHANGE observation_variable_id observation_variable_id INT NOT NULL');
        $this->addSql('ALTER TABLE observation_value ADD PRIMARY KEY (id, observation_level_id, observation_variable_id)');
        $this->addSql('ALTER TABLE study_parameter_value CHANGE id id INT NOT NULL');
    }
}
