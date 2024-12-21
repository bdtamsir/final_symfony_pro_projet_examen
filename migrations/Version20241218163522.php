<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218163522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_dette ADD client_id INT DEFAULT NULL, ADD articles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE demande_dette ADD CONSTRAINT FK_75C54B2119EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE demande_dette ADD CONSTRAINT FK_75C54B211EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id)');
        $this->addSql('CREATE INDEX IDX_75C54B2119EB6921 ON demande_dette (client_id)');
        $this->addSql('CREATE INDEX IDX_75C54B211EBAF6CC ON demande_dette (articles_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_dette DROP FOREIGN KEY FK_75C54B2119EB6921');
        $this->addSql('ALTER TABLE demande_dette DROP FOREIGN KEY FK_75C54B211EBAF6CC');
        $this->addSql('DROP INDEX IDX_75C54B2119EB6921 ON demande_dette');
        $this->addSql('DROP INDEX IDX_75C54B211EBAF6CC ON demande_dette');
        $this->addSql('ALTER TABLE demande_dette DROP client_id, DROP articles_id');
    }
}
