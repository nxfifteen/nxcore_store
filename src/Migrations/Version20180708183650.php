<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180708183650 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE life_tracked (id INT AUTO_INCREMENT NOT NULL, tracker INT DEFAULT NULL, date_time DATETIME DEFAULT NULL, lat VARCHAR(20) DEFAULT NULL, lon VARCHAR(20) DEFAULT NULL, value INT DEFAULT NULL, score INT DEFAULT NULL, INDEX IDX_FEC45E6DAC632AAF (tracker), UNIQUE INDEX Tracked (tracker, date_time, value), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE life_tracked ADD CONSTRAINT FK_FEC45E6DAC632AAF FOREIGN KEY (tracker) REFERENCES life_tracker (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE life_tracked');
    }
}
