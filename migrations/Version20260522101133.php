<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260522101133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_message (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) DEFAULT NULL, message CLOB NOT NULL, created_at DATETIME NOT NULL, is_read BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE film_series (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, image VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__film AS SELECT id, title, slug, description, image, video_url, video_file, published_at, is_published FROM film');
        $this->addSql('DROP TABLE film');
        $this->addSql('CREATE TABLE film (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, video_url VARCHAR(500) DEFAULT NULL, video_file VARCHAR(255) DEFAULT NULL, published_at DATETIME NOT NULL, is_published BOOLEAN NOT NULL, series_id INTEGER DEFAULT NULL, CONSTRAINT FK_8244BE225278319C FOREIGN KEY (series_id) REFERENCES film_series (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO film (id, title, slug, description, image, video_url, video_file, published_at, is_published) SELECT id, title, slug, description, image, video_url, video_file, published_at, is_published FROM __temp__film');
        $this->addSql('DROP TABLE __temp__film');
        $this->addSql('CREATE INDEX IDX_8244BE225278319C ON film (series_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE contact_message');
        $this->addSql('DROP TABLE film_series');
        $this->addSql('CREATE TEMPORARY TABLE __temp__film AS SELECT id, title, slug, description, image, video_url, video_file, published_at, is_published FROM film');
        $this->addSql('DROP TABLE film');
        $this->addSql('CREATE TABLE film (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, video_url VARCHAR(500) DEFAULT NULL, video_file VARCHAR(255) DEFAULT NULL, published_at DATETIME NOT NULL, is_published BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO film (id, title, slug, description, image, video_url, video_file, published_at, is_published) SELECT id, title, slug, description, image, video_url, video_file, published_at, is_published FROM __temp__film');
        $this->addSql('DROP TABLE __temp__film');
    }
}
