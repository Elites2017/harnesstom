<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220415134052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE observation_value MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE observation_value DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE observation_value CHANGE observation_level_id observation_level_id INT DEFAULT NULL, CHANGE observation_variable_id observation_variable_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE observation_value ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE study_parameter_value CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE observation_value MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE observation_value DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE observation_value CHANGE observation_level_id observation_level_id INT NOT NULL, CHANGE observation_variable_id observation_variable_id INT NOT NULL');
        $this->addSql('ALTER TABLE observation_value ADD PRIMARY KEY (id, observation_level_id, observation_variable_id)');
        $this->addSql('ALTER TABLE study_parameter_value CHANGE id id INT NOT NULL');
    }
}
