<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250619225940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_cours (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cours_id INT NOT NULL, firstname VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_1F0877C4A76ED395 (user_id), INDEX IDX_1F0877C47ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cours ADD CONSTRAINT FK_1F0877C4A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cours ADD CONSTRAINT FK_1F0877C47ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cours DROP FOREIGN KEY FK_1F0877C4A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cours DROP FOREIGN KEY FK_1F0877C47ECF78B0
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_cours
        SQL);
    }
}
