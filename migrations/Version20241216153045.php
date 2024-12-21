<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216153045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD dette_id INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66E11400A1 FOREIGN KEY (dette_id) REFERENCES dette (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66E11400A1 ON article (dette_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66E11400A1');
        $this->addSql('DROP INDEX IDX_23A0E66E11400A1 ON article');
        $this->addSql('ALTER TABLE article DROP dette_id');
    }
}
