<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210804081833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chatroom_user (chatroom_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(chatroom_id, user_id))');
        $this->addSql('CREATE INDEX IDX_F475AE7DCAF8A031 ON chatroom_user (chatroom_id)');
        $this->addSql('CREATE INDEX IDX_F475AE7DA76ED395 ON chatroom_user (user_id)');
        $this->addSql('ALTER TABLE chatroom_user ADD CONSTRAINT FK_F475AE7DCAF8A031 FOREIGN KEY (chatroom_id) REFERENCES chatroom (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chatroom_user ADD CONSTRAINT FK_F475AE7DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE chatroom_user');
    }
}
