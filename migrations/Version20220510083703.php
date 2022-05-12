<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510083703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mapping_population DROP FOREIGN KEY FK_68D3FF926836D5A8');
        $this->addSql('DROP INDEX IDX_68D3FF926836D5A8 ON mapping_population');
        $this->addSql('ALTER TABLE mapping_population DROP cross_id_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mapping_population ADD cross_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mapping_population ADD CONSTRAINT FK_68D3FF926836D5A8 FOREIGN KEY (cross_id_id) REFERENCES `cross` (id)');
        $this->addSql('CREATE INDEX IDX_68D3FF926836D5A8 ON mapping_population (cross_id_id)');
    }
}
