<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200318124854 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE purchase_order_product (purchase_order_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_F32214F9A45D7E6A (purchase_order_id), INDEX IDX_F32214F94584665A (product_id), PRIMARY KEY(purchase_order_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_order_product ADD CONSTRAINT FK_F32214F9A45D7E6A FOREIGN KEY (purchase_order_id) REFERENCES purchase_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchase_order_product ADD CONSTRAINT FK_F32214F94584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE purchase_order_product');
    }
}
