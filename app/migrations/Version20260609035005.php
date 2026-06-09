<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260609035005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ads CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tags ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_6FBC9426F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_6FBC9426F675F31B ON tags (author_id)');
        $this->addSql('ALTER TABLE topics ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE topics ADD CONSTRAINT FK_91F64639F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_91F64639F675F31B ON topics (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ads CHANGE author_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_6FBC9426F675F31B');
        $this->addSql('DROP INDEX IDX_6FBC9426F675F31B ON tags');
        $this->addSql('ALTER TABLE tags DROP author_id');
        $this->addSql('ALTER TABLE topics DROP FOREIGN KEY FK_91F64639F675F31B');
        $this->addSql('DROP INDEX IDX_91F64639F675F31B ON topics');
        $this->addSql('ALTER TABLE topics DROP author_id');
    }
}
