<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260621233631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ads (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, verified TINYINT NOT NULL, slug VARCHAR(255) NOT NULL, topic_id INT NOT NULL, author_id INT DEFAULT NULL, INDEX IDX_7EC9F6201F55203D (topic_id), INDEX IDX_7EC9F620F675F31B (author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ad_tag (ad_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_717EDDB44F34D596 (ad_id), INDEX IDX_717EDDB4BAD26311 (tag_id), PRIMARY KEY (ad_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(64) NOT NULL, slug VARCHAR(64) NOT NULL, author_id INT DEFAULT NULL, INDEX IDX_6FBC9426F675F31B (author_id), UNIQUE INDEX uq_tags_name (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE topics (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(64) NOT NULL, slug VARCHAR(64) NOT NULL, author_id INT DEFAULT NULL, INDEX IDX_91F64639F675F31B (author_id), UNIQUE INDEX uq_topics_name (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ads ADD CONSTRAINT FK_7EC9F6201F55203D FOREIGN KEY (topic_id) REFERENCES topics (id)');
        $this->addSql('ALTER TABLE ads ADD CONSTRAINT FK_7EC9F620F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE ad_tag ADD CONSTRAINT FK_717EDDB44F34D596 FOREIGN KEY (ad_id) REFERENCES ads (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ad_tag ADD CONSTRAINT FK_717EDDB4BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_6FBC9426F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE topics ADD CONSTRAINT FK_91F64639F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ads DROP FOREIGN KEY FK_7EC9F6201F55203D');
        $this->addSql('ALTER TABLE ads DROP FOREIGN KEY FK_7EC9F620F675F31B');
        $this->addSql('ALTER TABLE ad_tag DROP FOREIGN KEY FK_717EDDB44F34D596');
        $this->addSql('ALTER TABLE ad_tag DROP FOREIGN KEY FK_717EDDB4BAD26311');
        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_6FBC9426F675F31B');
        $this->addSql('ALTER TABLE topics DROP FOREIGN KEY FK_91F64639F675F31B');
        $this->addSql('DROP TABLE ads');
        $this->addSql('DROP TABLE ad_tag');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE topics');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
