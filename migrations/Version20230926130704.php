<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926130704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD phone VARCHAR(255) DEFAULT NULL, ADD profession VARCHAR(255) NOT NULL, ADD city VARCHAR(255) DEFAULT NULL, ADD zipcode VARCHAR(255) DEFAULT NULL, ADD street VARCHAR(255) DEFAULT NULL, ADD tva VARCHAR(255) DEFAULT NULL, ADD logo VARCHAR(255) DEFAULT NULL, ADD avatar VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP phone, DROP profession, DROP city, DROP zipcode, DROP street, DROP tva, DROP logo, DROP avatar');
    }
}
