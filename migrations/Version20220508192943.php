<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220508192943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Some place table modifications.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE place ADD admin1_code VARCHAR(20) DEFAULT NULL, ADD admin2_code VARCHAR(80) DEFAULT NULL, ADD admin3_code VARCHAR(20) DEFAULT NULL, ADD admin4_code VARCHAR(20) DEFAULT NULL, CHANGE modification_date modification_date DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE place DROP admin1_code, DROP admin2_code, DROP admin3_code, DROP admin4_code, CHANGE name name VARCHAR(1024) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE ascii_name ascii_name VARCHAR(1024) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE alternate_names alternate_names VARCHAR(4096) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE feature_class feature_class VARCHAR(1) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE feature_code feature_code VARCHAR(10) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE country_code country_code VARCHAR(2) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE cc2 cc2 VARCHAR(200) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE timezone timezone VARCHAR(40) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE modification_date modification_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
    }
}
