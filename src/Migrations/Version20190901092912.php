<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190901092912 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE consume_caffeine (id INT AUTO_INCREMENT NOT NULL, unit_of_measurement_id INT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, service_id INT NOT NULL, part_of_day_id INT NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, measurement DOUBLE PRECISION NOT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_2152F836DA7C9F35 (unit_of_measurement_id), INDEX IDX_2152F8366B899279 (patient_id), INDEX IDX_2152F8361984B75B (tracking_device_id), INDEX IDX_2152F836ED5CA9E6 (service_id), INDEX IDX_2152F836DEE7B414 (part_of_day_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consume_water (id INT AUTO_INCREMENT NOT NULL, unit_of_measurement_id INT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, service_id INT NOT NULL, part_of_day_id INT NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, measurement DOUBLE PRECISION NOT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_11CA8999DA7C9F35 (unit_of_measurement_id), INDEX IDX_11CA89996B899279 (patient_id), INDEX IDX_11CA89991984B75B (tracking_device_id), INDEX IDX_11CA8999ED5CA9E6 (service_id), INDEX IDX_11CA8999DEE7B414 (part_of_day_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_floors_intra_day (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value INT NOT NULL, INDEX IDX_60A6DB356B899279 (patient_id), INDEX IDX_60A6DB351984B75B (tracking_device_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_steps_daily_summary (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, part_of_day_id INT NOT NULL, patient_goal_id INT NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value INT NOT NULL, INDEX IDX_DE1CE7DF6B899279 (patient_id), INDEX IDX_DE1CE7DF1984B75B (tracking_device_id), INDEX IDX_DE1CE7DFDEE7B414 (part_of_day_id), INDEX IDX_DE1CE7DF3B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_steps_intra_day (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value INT NOT NULL, hour INT NOT NULL, duration INT DEFAULT NULL, INDEX IDX_D8852F156B899279 (patient_id), INDEX IDX_D8852F151984B75B (tracking_device_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE part_of_day (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1ADAD7EBD17F50A6 (uuid), UNIQUE INDEX UNIQ_1ADAD7EB7BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_goals (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, entity VARCHAR(255) NOT NULL, goal DOUBLE PRECISION NOT NULL, date_set DATETIME NOT NULL, INDEX IDX_5212C5E06B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE third_party_service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tracking_device (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, service_id INT NOT NULL, name VARCHAR(150) DEFAULT NULL, comment VARCHAR(200) DEFAULT NULL, battery INT DEFAULT NULL, last_synced DATETIME DEFAULT NULL, remote_id VARCHAR(255) DEFAULT NULL, type VARCHAR(150) DEFAULT NULL, manufacturer VARCHAR(255) DEFAULT NULL, model VARCHAR(255) DEFAULT NULL, INDEX IDX_A1A335C26B899279 (patient_id), INDEX IDX_A1A335C2ED5CA9E6 (service_id), UNIQUE INDEX DeviceService (remote_id, service_id, patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit_of_measurement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consume_caffeine ADD CONSTRAINT FK_2152F836DA7C9F35 FOREIGN KEY (unit_of_measurement_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE consume_caffeine ADD CONSTRAINT FK_2152F8366B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE consume_caffeine ADD CONSTRAINT FK_2152F8361984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE consume_caffeine ADD CONSTRAINT FK_2152F836ED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE consume_caffeine ADD CONSTRAINT FK_2152F836DEE7B414 FOREIGN KEY (part_of_day_id) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE consume_water ADD CONSTRAINT FK_11CA8999DA7C9F35 FOREIGN KEY (unit_of_measurement_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE consume_water ADD CONSTRAINT FK_11CA89996B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE consume_water ADD CONSTRAINT FK_11CA89991984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE consume_water ADD CONSTRAINT FK_11CA8999ED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE consume_water ADD CONSTRAINT FK_11CA8999DEE7B414 FOREIGN KEY (part_of_day_id) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE fit_floors_intra_day ADD CONSTRAINT FK_60A6DB356B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE fit_floors_intra_day ADD CONSTRAINT FK_60A6DB351984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE fit_steps_daily_summary ADD CONSTRAINT FK_DE1CE7DF6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE fit_steps_daily_summary ADD CONSTRAINT FK_DE1CE7DF1984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE fit_steps_daily_summary ADD CONSTRAINT FK_DE1CE7DFDEE7B414 FOREIGN KEY (part_of_day_id) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE fit_steps_daily_summary ADD CONSTRAINT FK_DE1CE7DF3B1E4F15 FOREIGN KEY (patient_goal_id) REFERENCES patient_goals (id)');
        $this->addSql('ALTER TABLE fit_steps_intra_day ADD CONSTRAINT FK_D8852F156B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE fit_steps_intra_day ADD CONSTRAINT FK_D8852F151984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE patient_goals ADD CONSTRAINT FK_5212C5E06B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE tracking_device ADD CONSTRAINT FK_A1A335C26B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE tracking_device ADD CONSTRAINT FK_A1A335C2ED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F836DEE7B414');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA8999DEE7B414');
        $this->addSql('ALTER TABLE fit_steps_daily_summary DROP FOREIGN KEY FK_DE1CE7DFDEE7B414');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F8366B899279');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA89996B899279');
        $this->addSql('ALTER TABLE fit_floors_intra_day DROP FOREIGN KEY FK_60A6DB356B899279');
        $this->addSql('ALTER TABLE fit_steps_daily_summary DROP FOREIGN KEY FK_DE1CE7DF6B899279');
        $this->addSql('ALTER TABLE fit_steps_intra_day DROP FOREIGN KEY FK_D8852F156B899279');
        $this->addSql('ALTER TABLE patient_goals DROP FOREIGN KEY FK_5212C5E06B899279');
        $this->addSql('ALTER TABLE tracking_device DROP FOREIGN KEY FK_A1A335C26B899279');
        $this->addSql('ALTER TABLE fit_steps_daily_summary DROP FOREIGN KEY FK_DE1CE7DF3B1E4F15');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F836ED5CA9E6');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA8999ED5CA9E6');
        $this->addSql('ALTER TABLE tracking_device DROP FOREIGN KEY FK_A1A335C2ED5CA9E6');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F8361984B75B');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA89991984B75B');
        $this->addSql('ALTER TABLE fit_floors_intra_day DROP FOREIGN KEY FK_60A6DB351984B75B');
        $this->addSql('ALTER TABLE fit_steps_daily_summary DROP FOREIGN KEY FK_DE1CE7DF1984B75B');
        $this->addSql('ALTER TABLE fit_steps_intra_day DROP FOREIGN KEY FK_D8852F151984B75B');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F836DA7C9F35');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA8999DA7C9F35');
        $this->addSql('DROP TABLE consume_caffeine');
        $this->addSql('DROP TABLE consume_water');
        $this->addSql('DROP TABLE fit_floors_intra_day');
        $this->addSql('DROP TABLE fit_steps_daily_summary');
        $this->addSql('DROP TABLE fit_steps_intra_day');
        $this->addSql('DROP TABLE part_of_day');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE patient_goals');
        $this->addSql('DROP TABLE third_party_service');
        $this->addSql('DROP TABLE tracking_device');
        $this->addSql('DROP TABLE unit_of_measurement');
    }
}
