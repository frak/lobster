<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140510183143 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE Feed (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, imageTitle VARCHAR(255) NOT NULL, imageUrl VARCHAR(255) NOT NULL, imageLink VARCHAR(255) NOT NULL, lastBuildDate DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE FeedItem (id INT AUTO_INCREMENT NOT NULL, feed_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, guid VARCHAR(255) NOT NULL, pubDate DATETIME NOT NULL, category VARCHAR(255) NOT NULL, enclosure VARCHAR(255) NOT NULL, INDEX IDX_F9B86D1951A5BC03 (feed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE FeedItem ADD CONSTRAINT FK_F9B86D1951A5BC03 FOREIGN KEY (feed_id) REFERENCES Feed (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE FeedItem DROP FOREIGN KEY FK_F9B86D1951A5BC03");
        $this->addSql("DROP TABLE Feed");
        $this->addSql("DROP TABLE FeedItem");
    }
}
