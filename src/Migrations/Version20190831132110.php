<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190831132110 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fit_steps_daily_summary (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, part_of_day_id INT NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value INT NOT NULL, goal INT DEFAULT NULL, INDEX IDX_DE1CE7DF6B899279 (patient_id), INDEX IDX_DE1CE7DF1984B75B (tracking_device_id), INDEX IDX_DE1CE7DFDEE7B414 (part_of_day_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE part_of_day (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE third_party_service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tracking_device (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, service_id INT NOT NULL, name VARCHAR(150) DEFAULT NULL, comment VARCHAR(200) DEFAULT NULL, battery INT DEFAULT NULL, last_synced DATETIME DEFAULT NULL, remote_id VARCHAR(255) DEFAULT NULL, type VARCHAR(150) DEFAULT NULL, manufacturer VARCHAR(255) NOT NULL, model VARCHAR(255) DEFAULT NULL, INDEX IDX_A1A335C26B899279 (patient_id), INDEX IDX_A1A335C2ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fit_steps_daily_summary ADD CONSTRAINT FK_DE1CE7DF6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE fit_steps_daily_summary ADD CONSTRAINT FK_DE1CE7DF1984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE fit_steps_daily_summary ADD CONSTRAINT FK_DE1CE7DFDEE7B414 FOREIGN KEY (part_of_day_id) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE tracking_device ADD CONSTRAINT FK_A1A335C26B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE tracking_device ADD CONSTRAINT FK_A1A335C2ED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE patient CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fit_steps_daily_summary DROP FOREIGN KEY FK_DE1CE7DFDEE7B414');
        $this->addSql('ALTER TABLE tracking_device DROP FOREIGN KEY FK_A1A335C2ED5CA9E6');
        $this->addSql('ALTER TABLE fit_steps_daily_summary DROP FOREIGN KEY FK_DE1CE7DF1984B75B');
        $this->addSql('DROP TABLE fit_steps_daily_summary');
        $this->addSql('DROP TABLE part_of_day');
        $this->addSql('DROP TABLE third_party_service');
        $this->addSql('DROP TABLE tracking_device');
        $this->addSql('ALTER TABLE patient CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
