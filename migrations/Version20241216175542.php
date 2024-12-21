<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216175542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dette_articles (dette_id INT NOT NULL, articles_id INT NOT NULL, INDEX IDX_7E7B24D0E11400A1 (dette_id), INDEX IDX_7E7B24D01EBAF6CC (articles_id), PRIMARY KEY(dette_id, articles_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dette_articles ADD CONSTRAINT FK_7E7B24D0E11400A1 FOREIGN KEY (dette_id) REFERENCES dette (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dette_articles ADD CONSTRAINT FK_7E7B24D01EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168E11400A1');
        $this->addSql('DROP INDEX IDX_BFDD3168E11400A1 ON articles');
        $this->addSql('ALTER TABLE articles DROP dette_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dette_articles DROP FOREIGN KEY FK_7E7B24D0E11400A1');
        $this->addSql('ALTER TABLE dette_articles DROP FOREIGN KEY FK_7E7B24D01EBAF6CC');
        $this->addSql('DROP TABLE dette_articles');
        $this->addSql('ALTER TABLE articles ADD dette_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168E11400A1 FOREIGN KEY (dette_id) REFERENCES dette (id)');
        $this->addSql('CREATE INDEX IDX_BFDD3168E11400A1 ON articles (dette_id)');
    }
}
