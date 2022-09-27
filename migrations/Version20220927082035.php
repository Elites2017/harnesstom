<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220927082035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE collection_class_germplasm');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE collection_class_germplasm (collection_class_id INT NOT NULL, germplasm_id INT NOT NULL, INDEX IDX_18D931D094DC88D9 (germplasm_id), INDEX IDX_18D931D01B80C520 (collection_class_id), PRIMARY KEY(collection_class_id, germplasm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE collection_class_germplasm ADD CONSTRAINT FK_18D931D094DC88D9 FOREIGN KEY (germplasm_id) REFERENCES germplasm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collection_class_germplasm ADD CONSTRAINT FK_18D931D01B80C520 FOREIGN KEY (collection_class_id) REFERENCES collection_class (id) ON DELETE CASCADE');
    }
}
