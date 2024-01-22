<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240115150223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock_data_matrix (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, product_family VARCHAR(255) DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, price VARCHAR(255) DEFAULT NULL, size1_name VARCHAR(255) DEFAULT NULL, size1 VARCHAR(255) DEFAULT NULL, size1_unit VARCHAR(255) DEFAULT NULL, size2_name VARCHAR(255) DEFAULT NULL, size2 VARCHAR(255) DEFAULT NULL, size2_unit VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stock CHANGE size1 size1 DOUBLE PRECISION DEFAULT NULL, CHANGE size2 size2 DOUBLE PRECISION DEFAULT NULL, CHANGE size1_unit size1_unit VARCHAR(255) DEFAULT NULL, CHANGE size2_unit size2_unit VARCHAR(255) DEFAULT NULL, CHANGE size1_name size1_name VARCHAR(255) DEFAULT NULL, CHANGE size2_name size2_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE stock_data_matrix');
        $this->addSql('ALTER TABLE stock CHANGE size1 size1 DOUBLE PRECISION NOT NULL, CHANGE size2 size2 DOUBLE PRECISION NOT NULL, CHANGE size1_unit size1_unit VARCHAR(255) NOT NULL, CHANGE size2_unit size2_unit VARCHAR(255) NOT NULL, CHANGE size1_name size1_name VARCHAR(255) NOT NULL, CHANGE size2_name size2_name VARCHAR(255) NOT NULL');
    }
}
