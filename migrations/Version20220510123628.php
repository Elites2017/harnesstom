<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510123628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE qtlstudy_study (qtlstudy_id INT NOT NULL, study_id INT NOT NULL, INDEX IDX_234C6A2A934CB70D (qtlstudy_id), INDEX IDX_234C6A2AE7B003E9 (study_id), PRIMARY KEY(qtlstudy_id, study_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE qtlstudy_study ADD CONSTRAINT FK_234C6A2A934CB70D FOREIGN KEY (qtlstudy_id) REFERENCES qtlstudy (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE qtlstudy_study ADD CONSTRAINT FK_234C6A2AE7B003E9 FOREIGN KEY (study_id) REFERENCES study (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE qtlstudy_study');
    }
}
