<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627152323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE attribute (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_variant (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, reserved INT NOT NULL, min_quantity INT NOT NULL, is_active TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_209AA41D4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE variant_attribute (id INT AUTO_INCREMENT NOT NULL, variant_id INT NOT NULL, attribute_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_198634A83B69A9AF (variant_id), INDEX IDX_198634A8B6E62EFA (attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_variant ADD CONSTRAINT FK_209AA41D4584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE variant_attribute ADD CONSTRAINT FK_198634A83B69A9AF FOREIGN KEY (variant_id) REFERENCES product_variant (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE variant_attribute ADD CONSTRAINT FK_198634A8B6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_option DROP FOREIGN KEY FK_38FA41144584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_option
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock_movement DROP FOREIGN KEY FK_BB1BC1B54584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BB1BC1B54584665A ON stock_movement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock_movement CHANGE product_id variant_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock_movement ADD CONSTRAINT FK_BB1BC1B53B69A9AF FOREIGN KEY (variant_id) REFERENCES product_variant (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BB1BC1B53B69A9AF ON stock_movement (variant_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE stock_movement DROP FOREIGN KEY FK_BB1BC1B53B69A9AF
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_option (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, value VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_38FA41144584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_option ADD CONSTRAINT FK_38FA41144584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_variant DROP FOREIGN KEY FK_209AA41D4584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE variant_attribute DROP FOREIGN KEY FK_198634A83B69A9AF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE variant_attribute DROP FOREIGN KEY FK_198634A8B6E62EFA
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE attribute
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_variant
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE variant_attribute
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BB1BC1B53B69A9AF ON stock_movement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock_movement CHANGE variant_id product_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock_movement ADD CONSTRAINT FK_BB1BC1B54584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BB1BC1B54584665A ON stock_movement (product_id)
        SQL);
    }
}
