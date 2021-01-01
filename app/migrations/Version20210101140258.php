<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210101140258 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscription_contents (id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', name VARCHAR(255) NOT NULL, year INT NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_types (id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', name VARCHAR(60) NOT NULL, price INT NOT NULL COMMENT \'Present data in coins of current currency\', period INT NOT NULL COMMENT \'Present data in days\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_type_content (subscription_type_id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', content_id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', INDEX IDX_B8B243CBB6596C08 (subscription_type_id), INDEX IDX_B8B243CB84A0A3ED (content_id), PRIMARY KEY(subscription_type_id, content_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_users (id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', subscription_content_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:identifier)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', activate_at DATE DEFAULT NULL, active TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_EEBBA49A4645E388 (subscription_content_id), INDEX IDX_EEBBA49AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_users (id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) NOT NULL, status VARCHAR(16) NOT NULL, role VARCHAR(16) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_F6415EB1E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscription_type_content ADD CONSTRAINT FK_B8B243CBB6596C08 FOREIGN KEY (subscription_type_id) REFERENCES subscription_types (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription_type_content ADD CONSTRAINT FK_B8B243CB84A0A3ED FOREIGN KEY (content_id) REFERENCES subscription_contents (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription_users ADD CONSTRAINT FK_EEBBA49A4645E388 FOREIGN KEY (subscription_content_id) REFERENCES subscription_contents (id)');
        $this->addSql('ALTER TABLE subscription_users ADD CONSTRAINT FK_EEBBA49AA76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_type_content DROP FOREIGN KEY FK_B8B243CB84A0A3ED');
        $this->addSql('ALTER TABLE subscription_users DROP FOREIGN KEY FK_EEBBA49A4645E388');
        $this->addSql('ALTER TABLE subscription_type_content DROP FOREIGN KEY FK_B8B243CBB6596C08');
        $this->addSql('ALTER TABLE subscription_users DROP FOREIGN KEY FK_EEBBA49AA76ED395');
        $this->addSql('DROP TABLE subscription_contents');
        $this->addSql('DROP TABLE subscription_types');
        $this->addSql('DROP TABLE subscription_type_content');
        $this->addSql('DROP TABLE subscription_users');
        $this->addSql('DROP TABLE user_users');
    }
}
