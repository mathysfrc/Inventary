<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230901122928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tracking (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, size1 DOUBLE PRECISION NOT NULL, size2 DOUBLE PRECISION NOT NULL, size1_unit VARCHAR(255) NOT NULL, size2_unit VARCHAR(255) NOT NULL, size1_name VARCHAR(255) NOT NULL, size2_name VARCHAR(255) NOT NULL, result_unit VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, product_family VARCHAR(255) NOT NULL, reference VARCHAR(255) DEFAULT NULL, free VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, timestamp DATETIME NOT NULL, movement_type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tracking');
    }
}
