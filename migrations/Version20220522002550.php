<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220522002550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create place tables.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE place_a (id INT AUTO_INCREMENT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(1024) NOT NULL, ascii_name VARCHAR(1024) NOT NULL, alternate_names VARCHAR(4096) NOT NULL, coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\', feature_class VARCHAR(1) NOT NULL, feature_code VARCHAR(10) NOT NULL, country_code VARCHAR(2) NOT NULL, cc2 VARCHAR(200) NOT NULL, population BIGINT DEFAULT NULL, elevation INT DEFAULT NULL, dem INT DEFAULT NULL, timezone VARCHAR(40) NOT NULL, modification_date DATE NOT NULL, admin1_code VARCHAR(20) DEFAULT NULL, admin2_code VARCHAR(80) DEFAULT NULL, admin3_code VARCHAR(20) DEFAULT NULL, admin4_code VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', SPATIAL INDEX coordinate_place_a (coordinate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_h (id INT AUTO_INCREMENT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(1024) NOT NULL, ascii_name VARCHAR(1024) NOT NULL, alternate_names VARCHAR(4096) NOT NULL, coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\', feature_class VARCHAR(1) NOT NULL, feature_code VARCHAR(10) NOT NULL, country_code VARCHAR(2) NOT NULL, cc2 VARCHAR(200) NOT NULL, population BIGINT DEFAULT NULL, elevation INT DEFAULT NULL, dem INT DEFAULT NULL, timezone VARCHAR(40) NOT NULL, modification_date DATE NOT NULL, admin1_code VARCHAR(20) DEFAULT NULL, admin2_code VARCHAR(80) DEFAULT NULL, admin3_code VARCHAR(20) DEFAULT NULL, admin4_code VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', SPATIAL INDEX coordinate_place_h (coordinate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_l (id INT AUTO_INCREMENT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(1024) NOT NULL, ascii_name VARCHAR(1024) NOT NULL, alternate_names VARCHAR(4096) NOT NULL, coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\', feature_class VARCHAR(1) NOT NULL, feature_code VARCHAR(10) NOT NULL, country_code VARCHAR(2) NOT NULL, cc2 VARCHAR(200) NOT NULL, population BIGINT DEFAULT NULL, elevation INT DEFAULT NULL, dem INT DEFAULT NULL, timezone VARCHAR(40) NOT NULL, modification_date DATE NOT NULL, admin1_code VARCHAR(20) DEFAULT NULL, admin2_code VARCHAR(80) DEFAULT NULL, admin3_code VARCHAR(20) DEFAULT NULL, admin4_code VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', SPATIAL INDEX coordinate_place_l (coordinate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_p (id INT AUTO_INCREMENT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(1024) NOT NULL, ascii_name VARCHAR(1024) NOT NULL, alternate_names VARCHAR(4096) NOT NULL, coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\', feature_class VARCHAR(1) NOT NULL, feature_code VARCHAR(10) NOT NULL, country_code VARCHAR(2) NOT NULL, cc2 VARCHAR(200) NOT NULL, population BIGINT DEFAULT NULL, elevation INT DEFAULT NULL, dem INT DEFAULT NULL, timezone VARCHAR(40) NOT NULL, modification_date DATE NOT NULL, admin1_code VARCHAR(20) DEFAULT NULL, admin2_code VARCHAR(80) DEFAULT NULL, admin3_code VARCHAR(20) DEFAULT NULL, admin4_code VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', SPATIAL INDEX coordinate_place_p (coordinate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_r (id INT AUTO_INCREMENT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(1024) NOT NULL, ascii_name VARCHAR(1024) NOT NULL, alternate_names VARCHAR(4096) NOT NULL, coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\', feature_class VARCHAR(1) NOT NULL, feature_code VARCHAR(10) NOT NULL, country_code VARCHAR(2) NOT NULL, cc2 VARCHAR(200) NOT NULL, population BIGINT DEFAULT NULL, elevation INT DEFAULT NULL, dem INT DEFAULT NULL, timezone VARCHAR(40) NOT NULL, modification_date DATE NOT NULL, admin1_code VARCHAR(20) DEFAULT NULL, admin2_code VARCHAR(80) DEFAULT NULL, admin3_code VARCHAR(20) DEFAULT NULL, admin4_code VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', SPATIAL INDEX coordinate_place_r (coordinate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_s (id INT AUTO_INCREMENT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(1024) NOT NULL, ascii_name VARCHAR(1024) NOT NULL, alternate_names VARCHAR(4096) NOT NULL, coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\', feature_class VARCHAR(1) NOT NULL, feature_code VARCHAR(10) NOT NULL, country_code VARCHAR(2) NOT NULL, cc2 VARCHAR(200) NOT NULL, population BIGINT DEFAULT NULL, elevation INT DEFAULT NULL, dem INT DEFAULT NULL, timezone VARCHAR(40) NOT NULL, modification_date DATE NOT NULL, admin1_code VARCHAR(20) DEFAULT NULL, admin2_code VARCHAR(80) DEFAULT NULL, admin3_code VARCHAR(20) DEFAULT NULL, admin4_code VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', SPATIAL INDEX coordinate_place_s (coordinate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_t (id INT AUTO_INCREMENT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(1024) NOT NULL, ascii_name VARCHAR(1024) NOT NULL, alternate_names VARCHAR(4096) NOT NULL, coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\', feature_class VARCHAR(1) NOT NULL, feature_code VARCHAR(10) NOT NULL, country_code VARCHAR(2) NOT NULL, cc2 VARCHAR(200) NOT NULL, population BIGINT DEFAULT NULL, elevation INT DEFAULT NULL, dem INT DEFAULT NULL, timezone VARCHAR(40) NOT NULL, modification_date DATE NOT NULL, admin1_code VARCHAR(20) DEFAULT NULL, admin2_code VARCHAR(80) DEFAULT NULL, admin3_code VARCHAR(20) DEFAULT NULL, admin4_code VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', SPATIAL INDEX coordinate_place_t (coordinate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_u (id INT AUTO_INCREMENT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(1024) NOT NULL, ascii_name VARCHAR(1024) NOT NULL, alternate_names VARCHAR(4096) NOT NULL, coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\', feature_class VARCHAR(1) NOT NULL, feature_code VARCHAR(10) NOT NULL, country_code VARCHAR(2) NOT NULL, cc2 VARCHAR(200) NOT NULL, population BIGINT DEFAULT NULL, elevation INT DEFAULT NULL, dem INT DEFAULT NULL, timezone VARCHAR(40) NOT NULL, modification_date DATE NOT NULL, admin1_code VARCHAR(20) DEFAULT NULL, admin2_code VARCHAR(80) DEFAULT NULL, admin3_code VARCHAR(20) DEFAULT NULL, admin4_code VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', SPATIAL INDEX coordinate_place_u (coordinate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_v (id INT AUTO_INCREMENT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(1024) NOT NULL, ascii_name VARCHAR(1024) NOT NULL, alternate_names VARCHAR(4096) NOT NULL, coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\', feature_class VARCHAR(1) NOT NULL, feature_code VARCHAR(10) NOT NULL, country_code VARCHAR(2) NOT NULL, cc2 VARCHAR(200) NOT NULL, population BIGINT DEFAULT NULL, elevation INT DEFAULT NULL, dem INT DEFAULT NULL, timezone VARCHAR(40) NOT NULL, modification_date DATE NOT NULL, admin1_code VARCHAR(20) DEFAULT NULL, admin2_code VARCHAR(80) DEFAULT NULL, admin3_code VARCHAR(20) DEFAULT NULL, admin4_code VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', SPATIAL INDEX coordinate_place_v (coordinate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE place_a');
        $this->addSql('DROP TABLE place_h');
        $this->addSql('DROP TABLE place_l');
        $this->addSql('DROP TABLE place_p');
        $this->addSql('DROP TABLE place_r');
        $this->addSql('DROP TABLE place_s');
        $this->addSql('DROP TABLE place_t');
        $this->addSql('DROP TABLE place_u');
        $this->addSql('DROP TABLE place_v');
    }
}
