<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260909004919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create User table with unique username and entity audit support.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, role VARCHAR(50) NOT NULL, username VARCHAR(180) NOT NULL, gender VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_2DA17977F85E0677 (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE revisions (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE User_audit (id INT NOT NULL, created_at DATETIME DEFAULT NULL, role VARCHAR(50) DEFAULT NULL, username VARCHAR(180) DEFAULT NULL, gender VARCHAR(20) DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_35655b38bc9a66eaa1b71d3bd7f3823b_idx (rev), PRIMARY KEY (id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE User_audit ADD CONSTRAINT rev_35655b38bc9a66eaa1b71d3bd7f3823b_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE User_audit DROP FOREIGN KEY rev_35655b38bc9a66eaa1b71d3bd7f3823b_fk');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE revisions');
        $this->addSql('DROP TABLE User_audit');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
