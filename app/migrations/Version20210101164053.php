<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210101164053 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_users DROP FOREIGN KEY FK_EEBBA49A4645E388');
        $this->addSql('DROP INDEX IDX_EEBBA49A4645E388 ON subscription_users');
        $this->addSql('ALTER TABLE subscription_users ADD subscription_id CHAR(36) NOT NULL COMMENT \'(DC2Type:identifier)\', DROP subscription_content_id');
        $this->addSql('ALTER TABLE subscription_users ADD CONSTRAINT FK_EEBBA49A9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription_types (id)');
        $this->addSql('CREATE INDEX IDX_EEBBA49A9A1887DC ON subscription_users (subscription_id)');
        $this->addSql('ALTER TABLE user_users CHANGE email email VARCHAR(255) NOT NULL, CHANGE role role VARCHAR(16) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_users DROP FOREIGN KEY FK_EEBBA49A9A1887DC');
        $this->addSql('DROP INDEX IDX_EEBBA49A9A1887DC ON subscription_users');
        $this->addSql('ALTER TABLE subscription_users ADD subscription_content_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:identifier)\', DROP subscription_id');
        $this->addSql('ALTER TABLE subscription_users ADD CONSTRAINT FK_EEBBA49A4645E388 FOREIGN KEY (subscription_content_id) REFERENCES subscription_contents (id)');
        $this->addSql('CREATE INDEX IDX_EEBBA49A4645E388 ON subscription_users (subscription_content_id)');
        $this->addSql('ALTER TABLE user_users CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE role role VARCHAR(16) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
