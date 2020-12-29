<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201229225243 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscriptions_types CHANGE price price INT NOT NULL COMMENT \'Present data in coins of current currency\', CHANGE period period INT NOT NULL COMMENT \'Present data in days\'');
        $this->addSql('ALTER TABLE users CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE role role VARCHAR(16) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscriptions_types CHANGE price price INT NOT NULL, CHANGE period period INT NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE role role VARCHAR(16) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
