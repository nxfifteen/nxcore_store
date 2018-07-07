<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180707203447 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reward (id INT AUTO_INCREMENT NOT NULL, service INT DEFAULT NULL, remote_id VARCHAR(50) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, value INT DEFAULT NULL, type VARCHAR(50) DEFAULT NULL, category VARCHAR(255) DEFAULT NULL, message LONGTEXT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, description_full LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, gradient_end_color VARCHAR(6) DEFAULT NULL, gradient_start_color VARCHAR(6) DEFAULT NULL, INDEX IDX_4ED17253E19D9AD2 (service), UNIQUE INDEX Reward (service, remote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reward ADD CONSTRAINT FK_4ED17253E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE reward');
    }
}
