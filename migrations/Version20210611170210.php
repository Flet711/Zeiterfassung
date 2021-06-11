<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210611170210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP INDEX project_id_uindex ON project');
        $this->addSql('ALTER TABLE project CHANGE statecode statecode TINYINT(1) NOT NULL');
        $this->addSql('DROP INDEX time_logging_id_uindex ON time_logging');
        $this->addSql('ALTER TABLE time_logging CHANGE enddate enddate DATETIME NOT NULL, CHANGE statecode statecode TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE project CHANGE statecode statecode TINYINT(1) DEFAULT \'1\'');
        $this->addSql('CREATE UNIQUE INDEX project_id_uindex ON project (id)');
        $this->addSql('ALTER TABLE time_logging CHANGE enddate enddate DATETIME DEFAULT NULL, CHANGE statecode statecode TINYINT(1) DEFAULT \'1\'');
        $this->addSql('CREATE UNIQUE INDEX time_logging_id_uindex ON time_logging (id)');
    }
}
