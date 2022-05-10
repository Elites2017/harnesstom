<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510084725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metabolite_value ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE metabolite_value ADD CONSTRAINT FK_66E8EA2DB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_66E8EA2DB03A8386 ON metabolite_value (created_by_id)');
        $this->addSql('ALTER TABLE qtlepistatistic_effect ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE qtlepistatistic_effect ADD CONSTRAINT FK_5C0F73B8B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5C0F73B8B03A8386 ON qtlepistatistic_effect (created_by_id)');
        $this->addSql('ALTER TABLE qtlstudy ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE qtlstudy ADD CONSTRAINT FK_4294152AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4294152AB03A8386 ON qtlstudy (created_by_id)');
        $this->addSql('ALTER TABLE qtlvariant ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE qtlvariant ADD CONSTRAINT FK_BC25C12EB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BC25C12EB03A8386 ON qtlvariant (created_by_id)');
        $this->addSql('ALTER TABLE variant_set ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE variant_set ADD CONSTRAINT FK_CB929A9FB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CB929A9FB03A8386 ON variant_set (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metabolite_value DROP FOREIGN KEY FK_66E8EA2DB03A8386');
        $this->addSql('DROP INDEX IDX_66E8EA2DB03A8386 ON metabolite_value');
        $this->addSql('ALTER TABLE metabolite_value DROP created_by_id');
        $this->addSql('ALTER TABLE qtlepistatistic_effect DROP FOREIGN KEY FK_5C0F73B8B03A8386');
        $this->addSql('DROP INDEX IDX_5C0F73B8B03A8386 ON qtlepistatistic_effect');
        $this->addSql('ALTER TABLE qtlepistatistic_effect DROP created_by_id');
        $this->addSql('ALTER TABLE qtlstudy DROP FOREIGN KEY FK_4294152AB03A8386');
        $this->addSql('DROP INDEX IDX_4294152AB03A8386 ON qtlstudy');
        $this->addSql('ALTER TABLE qtlstudy DROP created_by_id');
        $this->addSql('ALTER TABLE qtlvariant DROP FOREIGN KEY FK_BC25C12EB03A8386');
        $this->addSql('DROP INDEX IDX_BC25C12EB03A8386 ON qtlvariant');
        $this->addSql('ALTER TABLE qtlvariant DROP created_by_id');
        $this->addSql('ALTER TABLE variant_set DROP FOREIGN KEY FK_CB929A9FB03A8386');
        $this->addSql('DROP INDEX IDX_CB929A9FB03A8386 ON variant_set');
        $this->addSql('ALTER TABLE variant_set DROP created_by_id');
    }
}
