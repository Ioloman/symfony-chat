<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210906044134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chatroom ADD host_id INT');
        $this->addSql('ALTER TABLE chatroom ADD CONSTRAINT FK_1F3E6EC41FB8D185 FOREIGN KEY (host_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1F3E6EC41FB8D185 ON chatroom (host_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE chatroom DROP CONSTRAINT FK_1F3E6EC41FB8D185');
        $this->addSql('DROP INDEX IDX_1F3E6EC41FB8D185');
        $this->addSql('ALTER TABLE chatroom DROP host_id');
    }
}
