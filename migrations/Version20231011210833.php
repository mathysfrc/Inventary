<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011210833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, size1 DOUBLE PRECISION NOT NULL, size2 DOUBLE PRECISION NOT NULL, size1_unit VARCHAR(255) NOT NULL, size2_unit VARCHAR(255) NOT NULL, size1_name VARCHAR(255) NOT NULL, size2_name VARCHAR(255) NOT NULL, result_unit VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, product_family VARCHAR(255) NOT NULL, reference VARCHAR(255) DEFAULT NULL, free VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tracking (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, size1 DOUBLE PRECISION NOT NULL, size2 DOUBLE PRECISION NOT NULL, size1_unit VARCHAR(255) NOT NULL, size2_unit VARCHAR(255) NOT NULL, size1_name VARCHAR(255) NOT NULL, size2_name VARCHAR(255) NOT NULL, result_unit VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, product_family VARCHAR(255) NOT NULL, reference VARCHAR(255) DEFAULT NULL, free VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, timestamp DATETIME DEFAULT NULL, movement_type VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE tracking');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
