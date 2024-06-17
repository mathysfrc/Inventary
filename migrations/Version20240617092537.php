<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240617092537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock ADD boolean VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE stock_data_matrix DROP reference_month');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_data_matrix ADD reference_month VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE stock DROP boolean');
    }
}
