<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510083532 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mapping_population ADD cross_id_id INT DEFAULT NULL, ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mapping_population ADD CONSTRAINT FK_68D3FF926836D5A8 FOREIGN KEY (cross_id_id) REFERENCES `cross` (id)');
        $this->addSql('ALTER TABLE mapping_population ADD CONSTRAINT FK_68D3FF92B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_68D3FF926836D5A8 ON mapping_population (cross_id_id)');
        $this->addSql('CREATE INDEX IDX_68D3FF92B03A8386 ON mapping_population (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mapping_population DROP FOREIGN KEY FK_68D3FF926836D5A8');
        $this->addSql('ALTER TABLE mapping_population DROP FOREIGN KEY FK_68D3FF92B03A8386');
        $this->addSql('DROP INDEX IDX_68D3FF926836D5A8 ON mapping_population');
        $this->addSql('DROP INDEX IDX_68D3FF92B03A8386 ON mapping_population');
        $this->addSql('ALTER TABLE mapping_population DROP cross_id_id, DROP created_by_id');
    }
}
