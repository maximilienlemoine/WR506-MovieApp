<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218102740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_object (id INT AUTO_INCREMENT NOT NULL, movie_id INT DEFAULT NULL, actor_id INT DEFAULT NULL, file_path VARCHAR(255) DEFAULT NULL, INDEX IDX_14D431328F93B6FC (movie_id), INDEX IDX_14D4313210DAF24A (actor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media_object ADD CONSTRAINT FK_14D431328F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_object ADD CONSTRAINT FK_14D4313210DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD media_object_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64964DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64964DE5A5 ON user (media_object_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64964DE5A5');
        $this->addSql('ALTER TABLE media_object DROP FOREIGN KEY FK_14D431328F93B6FC');
        $this->addSql('ALTER TABLE media_object DROP FOREIGN KEY FK_14D4313210DAF24A');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('DROP INDEX UNIQ_8D93D64964DE5A5 ON user');
        $this->addSql('ALTER TABLE user DROP media_object_id');
    }
}
