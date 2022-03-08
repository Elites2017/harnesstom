<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220308142332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accession (id INT AUTO_INCREMENT NOT NULL, origcty_id INT DEFAULT NULL, collsrc_id INT DEFAULT NULL, sampstat_id INT DEFAULT NULL, taxon_id INT DEFAULT NULL, instcode_id INT DEFAULT NULL, storage_id INT DEFAULT NULL, donorcode_id INT DEFAULT NULL, collcode_id INT DEFAULT NULL, collmissid_id INT DEFAULT NULL, bredcode_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, accenumb VARCHAR(255) NOT NULL UNIQUE, accename VARCHAR(255) DEFAULT NULL, puid VARCHAR(255) NOT NULL UNIQUE, origmuni VARCHAR(255) DEFAULT NULL, origadmin1 VARCHAR(255) DEFAULT NULL, origadmin2 VARCHAR(255) DEFAULT NULL, maintainernumb VARCHAR(255) NOT NULL UNIQUE, acqdate DATE DEFAULT NULL, donornumb VARCHAR(255) NOT NULL UNIQUE, collnumb VARCHAR(255) DEFAULT NULL, colldate DATE DEFAULT NULL, declatitude NUMERIC(10, 6) DEFAULT NULL, declongitude NUMERIC(10, 6) DEFAULT NULL, elevation NUMERIC(10, 6) DEFAULT NULL, collsite VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_D983B0759183DDEC (origcty_id), INDEX IDX_D983B0752FE5821A (collsrc_id), INDEX IDX_D983B075A722ED46 (sampstat_id), INDEX IDX_D983B075DE13F470 (taxon_id), INDEX IDX_D983B075D4B26C81 (instcode_id), INDEX IDX_D983B0755CC5DB90 (storage_id), INDEX IDX_D983B0758B9A3ED4 (donorcode_id), INDEX IDX_D983B0755F8F805 (collcode_id), INDEX IDX_D983B075FC46E29A (collmissid_id), INDEX IDX_D983B07569608073 (bredcode_id), INDEX IDX_D983B075B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trial (id INT AUTO_INCREMENT NOT NULL, program_id INT DEFAULT NULL, trial_type_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, abbreviation VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, public_release_date DATE NOT NULL, license VARCHAR(255) DEFAULT NULL, pui VARCHAR(255) NOT NULL, publication_reference LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_74A25E3F3EB8070A (program_id), INDEX IDX_74A25E3FC685483F (trial_type_id), INDEX IDX_74A25E3FB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B0759183DDEC FOREIGN KEY (origcty_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B0752FE5821A FOREIGN KEY (collsrc_id) REFERENCES collecting_source (id)');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B075A722ED46 FOREIGN KEY (sampstat_id) REFERENCES biological_status (id)');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B075DE13F470 FOREIGN KEY (taxon_id) REFERENCES taxonomy (id)');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B075D4B26C81 FOREIGN KEY (instcode_id) REFERENCES institute (id)');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B0755CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage_type (id)');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B0758B9A3ED4 FOREIGN KEY (donorcode_id) REFERENCES institute (id)');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B0755F8F805 FOREIGN KEY (collcode_id) REFERENCES institute (id)');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B075FC46E29A FOREIGN KEY (collmissid_id) REFERENCES collecting_mission (id)');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B07569608073 FOREIGN KEY (bredcode_id) REFERENCES institute (id)');
        $this->addSql('ALTER TABLE accession ADD CONSTRAINT FK_D983B075B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trial ADD CONSTRAINT FK_74A25E3F3EB8070A FOREIGN KEY (program_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE trial ADD CONSTRAINT FK_74A25E3FC685483F FOREIGN KEY (trial_type_id) REFERENCES trial_type (id)');
        $this->addSql('ALTER TABLE trial ADD CONSTRAINT FK_74A25E3FB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE accession');
        $this->addSql('DROP TABLE trial');
    }
}
