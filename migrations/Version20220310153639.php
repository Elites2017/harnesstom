<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220310153639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE qtlepistatistic_effect (id INT AUTO_INCREMENT NOT NULL, qtl_variant1_id INT DEFAULT NULL, qtl_variant2_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, add_epi DOUBLE PRECISION DEFAULT NULL, r2_add DOUBLE PRECISION DEFAULT NULL, r2_epi DOUBLE PRECISION DEFAULT NULL, epistatistic_epi DOUBLE PRECISION DEFAULT NULL, INDEX IDX_5C0F73B8BAA8D38A (qtl_variant1_id), INDEX IDX_5C0F73B8A81D7C64 (qtl_variant2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE qtlepistatistic_effect ADD CONSTRAINT FK_5C0F73B8BAA8D38A FOREIGN KEY (qtl_variant1_id) REFERENCES qtlvariant (id)');
        $this->addSql('ALTER TABLE qtlepistatistic_effect ADD CONSTRAINT FK_5C0F73B8A81D7C64 FOREIGN KEY (qtl_variant2_id) REFERENCES qtlvariant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE qtlepistatistic_effect');
    }
}
