<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510084341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metabolite_value DROP created_by');
        $this->addSql('ALTER TABLE qtlepistatistic_effect DROP created_by');
        $this->addSql('ALTER TABLE qtlstudy DROP created_by');
        $this->addSql('ALTER TABLE qtlvariant DROP created_by');
        $this->addSql('ALTER TABLE variant_set DROP created_by');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metabolite_value ADD created_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE qtlepistatistic_effect ADD created_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE qtlstudy ADD created_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE qtlvariant ADD created_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE variant_set ADD created_by VARCHAR(255) DEFAULT NULL');
    }
}
