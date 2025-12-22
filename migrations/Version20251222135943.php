<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251222135943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY `FK_97A0ADA3895ECCB8`');
        $this->addSql('DROP INDEX IDX_97A0ADA3895ECCB8 ON ticket');
        $this->addSql('ALTER TABLE ticket ADD status_id INT NOT NULL, CHANGE closed_at closed_at DATETIME DEFAULT NULL, CHANGE responsile_id responsible_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA36BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3602AD315 FOREIGN KEY (responsible_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA36BF700BD ON ticket (status_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3602AD315 ON ticket (responsible_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA36BF700BD');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3602AD315');
        $this->addSql('DROP INDEX IDX_97A0ADA36BF700BD ON ticket');
        $this->addSql('DROP INDEX IDX_97A0ADA3602AD315 ON ticket');
        $this->addSql('ALTER TABLE ticket DROP status_id, CHANGE closed_at closed_at DATETIME NOT NULL, CHANGE responsible_id responsile_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT `FK_97A0ADA3895ECCB8` FOREIGN KEY (responsile_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_97A0ADA3895ECCB8 ON ticket (responsile_id)');
    }
}
