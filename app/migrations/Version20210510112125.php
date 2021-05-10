<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210510112125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE command (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `groups` (id INT AUTO_INCREMENT NOT NULL, tourney_id INT NOT NULL, group_letter VARCHAR(1) NOT NULL, team_id INT NOT NULL, points INT NOT NULL DEFAULT 0, INDEX IDX_F06D3970ECAE3834 (tourney_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matches (id INT AUTO_INCREMENT NOT NULL, team_home_id INT NOT NULL, team_away_id INT NOT NULL, tourney_id INT NOT NULL, round VARCHAR(1) NOT NULL, score_home INT DEFAULT NULL, score_away INT DEFAULT NULL, group_letter VARCHAR(1) DEFAULT NULL, play_off_round INT DEFAULT NULL, INDEX IDX_62615BAD7B4B9AB (team_home_id), INDEX IDX_62615BA729679A8 (team_away_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tourney (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `groups` ADD CONSTRAINT FK_F06D3970ECAE3834 FOREIGN KEY (tourney_id) REFERENCES tourney (id)');
        $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BAD7B4B9AB FOREIGN KEY (team_home_id) REFERENCES command (id)');
        $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BA729679A8 FOREIGN KEY (team_away_id) REFERENCES command (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matches DROP FOREIGN KEY FK_62615BAD7B4B9AB');
        $this->addSql('ALTER TABLE matches DROP FOREIGN KEY FK_62615BA729679A8');
        $this->addSql('ALTER TABLE `groups` DROP FOREIGN KEY FK_F06D3970ECAE3834');
        $this->addSql('DROP TABLE command');
        $this->addSql('DROP TABLE `groups`');
        $this->addSql('DROP TABLE matches');
        $this->addSql('DROP TABLE tourney');
    }
}
