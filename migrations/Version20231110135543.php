<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231110135543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock ADD shape VARCHAR(255) DEFAULT NULL, CHANGE result_unit surface VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tracking ADD surface VARCHAR(255) DEFAULT NULL, ADD shape VARCHAR(255) DEFAULT NULL, DROP result_unit');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tracking ADD result_unit VARCHAR(255) NOT NULL, DROP surface, DROP shape');
        $this->addSql('ALTER TABLE stock ADD result_unit VARCHAR(255) DEFAULT NULL, DROP surface, DROP shape');
    }
}
