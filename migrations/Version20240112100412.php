<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240112100412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checkout ADD description LONGTEXT DEFAULT NULL, ADD size1 VARCHAR(255) DEFAULT NULL, ADD size2 VARCHAR(255) DEFAULT NULL, ADD size1_unit VARCHAR(255) DEFAULT NULL, ADD size2_unit VARCHAR(255) DEFAULT NULL, ADD size1_name VARCHAR(255) DEFAULT NULL, ADD size2_name VARCHAR(255) DEFAULT NULL, ADD price VARCHAR(255) DEFAULT NULL, ADD product_family VARCHAR(255) DEFAULT NULL, ADD status VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checkout DROP description, DROP size1, DROP size2, DROP size1_unit, DROP size2_unit, DROP size1_name, DROP size2_name, DROP price, DROP product_family, DROP status');
    }
}
