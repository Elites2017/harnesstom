<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220506095609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE germplasm_study_image ADD germplasm_id_id INT DEFAULT NULL, ADD study_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE germplasm_study_image ADD CONSTRAINT FK_36B2A923982647A7 FOREIGN KEY (germplasm_id_id) REFERENCES germplasm (id)');
        $this->addSql('ALTER TABLE germplasm_study_image ADD CONSTRAINT FK_36B2A923203ECEB7 FOREIGN KEY (study_id_id) REFERENCES study (id)');
        $this->addSql('CREATE INDEX IDX_36B2A923982647A7 ON germplasm_study_image (germplasm_id_id)');
        $this->addSql('CREATE INDEX IDX_36B2A923203ECEB7 ON germplasm_study_image (study_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE germplasm_study_image DROP FOREIGN KEY FK_36B2A923982647A7');
        $this->addSql('ALTER TABLE germplasm_study_image DROP FOREIGN KEY FK_36B2A923203ECEB7');
        $this->addSql('DROP INDEX IDX_36B2A923982647A7 ON germplasm_study_image');
        $this->addSql('DROP INDEX IDX_36B2A923203ECEB7 ON germplasm_study_image');
        $this->addSql('ALTER TABLE germplasm_study_image DROP germplasm_id_id, DROP study_id_id');
    }
}
