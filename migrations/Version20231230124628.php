<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231230124628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP INDEX IDX_C53D045F4B89032C, ADD UNIQUE INDEX UNIQ_C53D045F4B89032C (post_id)');
        $this->addSql('ALTER TABLE image CHANGE post_id post_id INT NOT NULL, CHANGE name image_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP INDEX UNIQ_C53D045F4B89032C, ADD INDEX IDX_C53D045F4B89032C (post_id)');
        $this->addSql('ALTER TABLE image CHANGE post_id post_id INT DEFAULT NULL, CHANGE image_name name VARCHAR(255) NOT NULL');
    }
}
