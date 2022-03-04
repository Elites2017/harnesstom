<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220303152438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attribute (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, abbreviation VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, publication_reference LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_FA7AEFFB12469DE2 (category_id), INDEX IDX_FA7AEFFBB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE institute (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, instcode VARCHAR(255) NOT NULL UNIQUE, acronym VARCHAR(255) NOT NULL UNIQUE, name VARCHAR(255) NOT NULL, street_number VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_CA55B5D0F92F3E70 (country_id), INDEX IDX_CA55B5D0B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, country_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, longitude_co NUMERIC(10, 6) DEFAULT NULL, latitude_co NUMERIC(10, 6) DEFAULT NULL, altitude_co NUMERIC(10, 6) DEFAULT NULL, site_status VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_5E9E89CBB03A8386 (created_by_id), INDEX IDX_5E9E89CBF92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE observation_variable_method (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, method_class_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, instrument VARCHAR(255) DEFAULT NULL, software VARCHAR(255) DEFAULT NULL, publication_reference LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_C50E7543B03A8386 (created_by_id), INDEX IDX_C50E7543C674582 (method_class_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parameter (id INT AUTO_INCREMENT NOT NULL, factor_type_id INT DEFAULT NULL, unit_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_2A9791104D692723 (factor_type_id), INDEX IDX_2A979110F8BD700D (unit_id), INDEX IDX_2A979110B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, phone_number VARCHAR(255) DEFAULT NULL, street_number VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_34DCD176F92F3E70 (country_id), UNIQUE INDEX UNIQ_34DCD176A76ED395 (user_id), INDEX IDX_34DCD176B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scale (id INT AUTO_INCREMENT NOT NULL, scale_category_id INT DEFAULT NULL, data_type_id INT DEFAULT NULL, unit_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_EC46258497575D0E (scale_category_id), INDEX IDX_EC462584A147DA62 (data_type_id), INDEX IDX_EC462584F8BD700D (unit_id), INDEX IDX_EC462584B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attribute ADD CONSTRAINT FK_FA7AEFFB12469DE2 FOREIGN KEY (category_id) REFERENCES attribute_category (id)');
        $this->addSql('ALTER TABLE attribute ADD CONSTRAINT FK_FA7AEFFBB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE institute ADD CONSTRAINT FK_CA55B5D0F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE institute ADD CONSTRAINT FK_CA55B5D0B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE observation_variable_method ADD CONSTRAINT FK_C50E7543B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE observation_variable_method ADD CONSTRAINT FK_C50E7543C674582 FOREIGN KEY (method_class_id) REFERENCES method_class (id)');
        $this->addSql('ALTER TABLE parameter ADD CONSTRAINT FK_2A9791104D692723 FOREIGN KEY (factor_type_id) REFERENCES factor_type (id)');
        $this->addSql('ALTER TABLE parameter ADD CONSTRAINT FK_2A979110F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE parameter ADD CONSTRAINT FK_2A979110B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE scale ADD CONSTRAINT FK_EC46258497575D0E FOREIGN KEY (scale_category_id) REFERENCES scale_category (id)');
        $this->addSql('ALTER TABLE scale ADD CONSTRAINT FK_EC462584A147DA62 FOREIGN KEY (data_type_id) REFERENCES data_type (id)');
        $this->addSql('ALTER TABLE scale ADD CONSTRAINT FK_EC462584F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE scale ADD CONSTRAINT FK_EC462584B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX ontology_id ON anatomical_entity');
        $this->addSql('DROP INDEX ontology_id ON breeding_method');
        $this->addSql('DROP INDEX ontology_id ON ci_criteria');
        $this->addSql('DROP INDEX ontology_id ON developmental_stage');
        $this->addSql('DROP INDEX ontology_id ON experimental_design_type');
        $this->addSql('DROP INDEX ontology_id ON factor_type');
        $this->addSql('DROP INDEX ontology_id ON growth_facility_type');
        $this->addSql('DROP INDEX ontology_id ON gwasmodel');
        $this->addSql('DROP INDEX ontology_id ON gwasstat_test');
        $this->addSql('DROP INDEX ontology_id ON kinship_algorithm');
        $this->addSql('DROP INDEX ontology_id ON qtlmethod');
        $this->addSql('DROP INDEX ontology_id ON software');
        $this->addSql('DROP INDEX ontology_id ON structure_method');
        $this->addSql('DROP INDEX ontology_id ON threshold_method');
        $this->addSql('DROP INDEX ontology_id ON trait_class');
        $this->addSql('DROP INDEX ontology_id ON unit');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE attribute');
        $this->addSql('DROP TABLE institute');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE observation_variable_method');
        $this->addSql('DROP TABLE parameter');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE scale');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON anatomical_entity (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON breeding_method (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON ci_criteria (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON developmental_stage (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON experimental_design_type (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON factor_type (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON growth_facility_type (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON gwasmodel (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON gwasstat_test (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON kinship_algorithm (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON qtlmethod (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON software (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON structure_method (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON threshold_method (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON trait_class (ontology_id)');
        $this->addSql('CREATE UNIQUE INDEX ontology_id ON unit (ontology_id)');
    }
}
