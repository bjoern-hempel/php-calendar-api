<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220508005037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds place table.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(1024) NOT NULL, ascii_name VARCHAR(1024) NOT NULL, alternate_names VARCHAR(4096) NOT NULL, coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\', feature_class VARCHAR(1) NOT NULL, feature_code VARCHAR(10) NOT NULL, country_code VARCHAR(2) NOT NULL, cc2 VARCHAR(200) NOT NULL, population BIGINT DEFAULT NULL, elevation INT DEFAULT NULL, dem INT DEFAULT NULL, timezone VARCHAR(40) NOT NULL, modification_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE place');
    }
}
