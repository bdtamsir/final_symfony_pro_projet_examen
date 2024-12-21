<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241213144530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dette (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, montant DOUBLE PRECISION NOT NULL, montant_verser DOUBLE PRECISION NOT NULL, date_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_831BC80819EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dette ADD CONSTRAINT FK_831BC80819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455E7769B0F ON client (surname)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455450FF010 ON client (telephone)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dette DROP FOREIGN KEY FK_831BC80819EB6921');
        $this->addSql('DROP TABLE dette');
        $this->addSql('DROP INDEX UNIQ_C7440455E7769B0F ON client');
        $this->addSql('DROP INDEX UNIQ_C7440455450FF010 ON client');
    }
}
