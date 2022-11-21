<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220522162952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds more fields to image table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE image ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, ADD title VARCHAR(255) DEFAULT NULL, ADD url VARCHAR(255) DEFAULT NULL, ADD gps_height INT DEFAULT NULL, ADD iso INT DEFAULT NULL, ADD mime VARCHAR(63) DEFAULT NULL, ADD place VARCHAR(255) DEFAULT NULL, ADD place_city VARCHAR(255) DEFAULT NULL, ADD place_state VARCHAR(255) DEFAULT NULL, ADD place_country VARCHAR(255) DEFAULT NULL, ADD place_timezone VARCHAR(255) DEFAULT NULL, ADD information LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD taken_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE image DROP latitude, DROP longitude, DROP title, DROP url, DROP gps_height, DROP iso, DROP mime, DROP place, DROP place_city, DROP place_state, DROP place_country, DROP place_timezone, DROP information, DROP taken_at, CHANGE path path VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}