<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201227210212 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE content (id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', name VARCHAR(255) NOT NULL, year INT NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_type (id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', name VARCHAR(60) NOT NULL, price INT NOT NULL, period INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_type_content (subscription_type_id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', content_id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', INDEX IDX_B8B243CBB6596C08 (subscription_type_id), INDEX IDX_B8B243CB84A0A3ED (content_id), PRIMARY KEY(subscription_type_id, content_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_user (id INT AUTO_INCREMENT NOT NULL, subscription_content_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:identifier)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', activate_at DATE DEFAULT NULL, active TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_BAAFC6574645E388 (subscription_content_id), INDEX IDX_BAAFC657A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(16) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscription_type_content ADD CONSTRAINT FK_B8B243CBB6596C08 FOREIGN KEY (subscription_type_id) REFERENCES subscription_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription_type_content ADD CONSTRAINT FK_B8B243CB84A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription_user ADD CONSTRAINT FK_BAAFC6574645E388 FOREIGN KEY (subscription_content_id) REFERENCES content (id)');
        $this->addSql('ALTER TABLE subscription_user ADD CONSTRAINT FK_BAAFC657A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE post');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_type_content DROP FOREIGN KEY FK_B8B243CB84A0A3ED');
        $this->addSql('ALTER TABLE subscription_user DROP FOREIGN KEY FK_BAAFC6574645E388');
        $this->addSql('ALTER TABLE subscription_type_content DROP FOREIGN KEY FK_B8B243CBB6596C08');
        $this->addSql('ALTER TABLE subscription_user DROP FOREIGN KEY FK_BAAFC657A76ED395');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, create_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE subscription_type');
        $this->addSql('DROP TABLE subscription_type_content');
        $this->addSql('DROP TABLE subscription_user');
        $this->addSql('DROP TABLE user');
    }
}
