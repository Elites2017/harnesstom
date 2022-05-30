<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220524101946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anatomical_entity CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE attribute CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE attribute_category CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE breeding_method CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE collection_class CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE `cross` CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE developmental_stage CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE factor_type CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE genotyping_platform CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE germplasm_study_image CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE growth_facility_type CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE metabolic_trait CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE method_class CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE observation_variable CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE observation_variable_method CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE sample CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE scale CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE season CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE study CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE study_image CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE trait_class CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE trait_processing CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE trial CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE trial_type CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE unit CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE variant_set_metadata CHANGE description description LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anatomical_entity CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE attribute CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE attribute_category CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE breeding_method CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE collection_class CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `cross` CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE developmental_stage CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE factor_type CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE genotyping_platform CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE germplasm_study_image CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE growth_facility_type CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE metabolic_trait CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE method_class CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE observation_variable CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE observation_variable_method CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sample CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE scale CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE season CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE study CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE study_image CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE trait_class CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE trait_processing CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE trial CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE trial_type CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE unit CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE variant_set_metadata CHANGE description description VARCHAR(255) DEFAULT NULL');
    }
}
