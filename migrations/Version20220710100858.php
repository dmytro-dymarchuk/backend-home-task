<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220710100858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial DB schema.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lock_file (id INT AUTO_INCREMENT NOT NULL, upload_id INT DEFAULT NULL, file_name VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_9A60CCFECCCFBA31 (upload_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE upload (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lock_file ADD CONSTRAINT FK_9A60CCFECCCFBA31 FOREIGN KEY (upload_id) REFERENCES upload (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lock_file DROP FOREIGN KEY FK_9A60CCFECCCFBA31');
        $this->addSql('DROP TABLE lock_file');
        $this->addSql('DROP TABLE upload');
    }
}
