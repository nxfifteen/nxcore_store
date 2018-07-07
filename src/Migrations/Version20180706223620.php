<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180706223620 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activity_level (id INT AUTO_INCREMENT NOT NULL, sedentary INT DEFAULT NULL, lightly INT DEFAULT NULL, fairly INT DEFAULT NULL, very INT DEFAULT NULL, UNIQUE INDEX UniqueReading (sedentary, lightly, fairly, very), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE body_bmi (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, measurement DOUBLE PRECISION DEFAULT NULL, date_time DATE DEFAULT NULL, INDEX IDX_D1B547FD6B899279 (patient_id), UNIQUE INDEX DateReading (date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE body_fat (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, unit INT DEFAULT NULL, part_of_day INT DEFAULT NULL, measurement DOUBLE PRECISION DEFAULT NULL, goal DOUBLE PRECISION DEFAULT NULL, date_time DATE DEFAULT NULL, INDEX IDX_190FCCF46B899279 (patient_id), INDEX IDX_190FCCF4DCBB0C53 (unit), INDEX IDX_190FCCF4EE0DB122 (part_of_day), UNIQUE INDEX DateReading (date_time, patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE body_weight (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, unit INT DEFAULT NULL, part_of_day INT DEFAULT NULL, measurement DOUBLE PRECISION DEFAULT NULL, goal DOUBLE PRECISION DEFAULT NULL, date_time DATE DEFAULT NULL, INDEX IDX_92D1F6476B899279 (patient_id), INDEX IDX_92D1F647DCBB0C53 (unit), INDEX IDX_92D1F647EE0DB122 (part_of_day), UNIQUE INDEX DateReading (date_time, patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE count_daily_calories (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, goal INT DEFAULT NULL, INDEX IDX_27AF31BC6B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE count_daily_distance (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, unit INT DEFAULT NULL, date_time DATE DEFAULT NULL, value DOUBLE PRECISION DEFAULT NULL, goal DOUBLE PRECISION DEFAULT NULL, INDEX IDX_1E589C3C6B899279 (patient_id), INDEX IDX_1E589C3CDCBB0C53 (unit), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE count_daily_elevation (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, INDEX IDX_89F84FB66B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE count_daily_floor (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, goal INT DEFAULT NULL, INDEX IDX_4DE77C7D6B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE count_daily_step (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, goal INT DEFAULT NULL, INDEX IDX_5862E52E6B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE heart_rate (id INT AUTO_INCREMENT NOT NULL, out_of_range_id INT DEFAULT NULL, fat_burn_id INT DEFAULT NULL, cardio_id INT DEFAULT NULL, peak_id INT DEFAULT NULL, average INT DEFAULT NULL, INDEX IDX_84A07ED666951C68 (out_of_range_id), INDEX IDX_84A07ED64AA71F69 (fat_burn_id), INDEX IDX_84A07ED619536BEF (cardio_id), INDEX IDX_84A07ED6F848A361 (peak_id), UNIQUE INDEX UniqueReading (average, out_of_range_id, fat_burn_id, cardio_id, peak_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE heart_rate_cardio (id INT AUTO_INCREMENT NOT NULL, average INT DEFAULT NULL, min INT DEFAULT NULL, max INT DEFAULT NULL, time INT DEFAULT NULL, UNIQUE INDEX UniqueReading (average, min, max, time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE heart_rate_fat_burn (id INT AUTO_INCREMENT NOT NULL, average INT DEFAULT NULL, min INT DEFAULT NULL, max INT DEFAULT NULL, time INT DEFAULT NULL, UNIQUE INDEX UniqueReading (average, min, max, time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE heart_rate_out_of_range (id INT AUTO_INCREMENT NOT NULL, average INT DEFAULT NULL, min INT DEFAULT NULL, max INT DEFAULT NULL, time INT DEFAULT NULL, UNIQUE INDEX UniqueReading (average, min, max, time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE heart_rate_peak (id INT AUTO_INCREMENT NOT NULL, average INT DEFAULT NULL, min INT DEFAULT NULL, max INT DEFAULT NULL, time INT DEFAULT NULL, UNIQUE INDEX UniqueReading (average, min, max, time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE min_daily_fairly (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, INDEX IDX_415CE5B46B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE min_daily_lightly (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, INDEX IDX_DAA1CD006B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE min_daily_sedentary (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, INDEX IDX_94B437886B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE min_daily_very (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, goal INT DEFAULT NULL, INDEX IDX_FB8F4C6F6B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nutrition_information (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, unit INT DEFAULT NULL, date_time DATE NOT NULL, period VARCHAR(4) NOT NULL, amount INT DEFAULT NULL, brand VARCHAR(50) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, meal VARCHAR(50) DEFAULT NULL, calories INT DEFAULT NULL, goal INT DEFAULT NULL, carbs INT DEFAULT NULL, fat INT DEFAULT NULL, fiber INT DEFAULT NULL, protein INT DEFAULT NULL, sodium INT DEFAULT NULL, water INT DEFAULT NULL, goal_calories_out INT DEFAULT NULL, INDEX IDX_47B586C86B899279 (patient_id), INDEX IDX_47B586C8DCBB0C53 (unit), UNIQUE INDEX DateReading (patient_id, date_time, period, meal, amount, unit, brand, calories), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE part_of_day (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(9) NOT NULL, UNIQUE INDEX UNIQ_EE0DB1225E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, fname VARCHAR(255) DEFAULT NULL, lname VARCHAR(255) DEFAULT NULL, birthday DATETIME DEFAULT NULL, height INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, gender VARCHAR(6) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport_activity (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, source INT DEFAULT NULL, heart_rate_id INT DEFAULT NULL, activity_level_id INT DEFAULT NULL, part_of_day INT DEFAULT NULL, sport_track INT NOT NULL, remote_id BIGINT DEFAULT NULL, activity_name VARCHAR(255) DEFAULT NULL, start_time DATETIME DEFAULT NULL, duration INT DEFAULT NULL, INDEX IDX_9AD6F6E06B899279 (patient_id), INDEX IDX_9AD6F6E05F8A7F73 (source), INDEX IDX_9AD6F6E0882F7BA6 (heart_rate_id), INDEX IDX_9AD6F6E0FFBBFBA9 (activity_level_id), INDEX IDX_9AD6F6E0EE0DB122 (part_of_day), UNIQUE INDEX UNIQ_9AD6F6E0629F99BF (sport_track), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport_activity_source (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport_track (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, tracking_device INT DEFAULT NULL, start_time DATETIME DEFAULT NULL, total_time DOUBLE PRECISION DEFAULT NULL, total_distance DOUBLE PRECISION DEFAULT NULL, calories INT DEFAULT NULL, intensity VARCHAR(255) DEFAULT NULL, method VARCHAR(255) DEFAULT NULL, INDEX IDX_629F99BF6B899279 (patient_id), INDEX IDX_629F99BFA1A335C2 (tracking_device), UNIQUE INDEX ActivityTrack (total_time, start_time, total_distance, patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport_track_point (id INT AUTO_INCREMENT NOT NULL, sport_track_id INT DEFAULT NULL, time DATETIME DEFAULT NULL, lat VARCHAR(20) DEFAULT NULL, lon VARCHAR(20) DEFAULT NULL, altitude NUMERIC(10, 0) DEFAULT NULL, distrance INT DEFAULT NULL, heart_rate INT DEFAULT NULL, INDEX IDX_A46715C56870C3F9 (sport_track_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE third_party_relations (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, service INT DEFAULT NULL, username VARCHAR(30) DEFAULT NULL, INDEX IDX_23BD673D6B899279 (patient_id), INDEX IDX_23BD673DE19D9AD2 (service), UNIQUE INDEX RemoteUserName (patient_id, username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE third_party_service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tracking_device (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, name VARCHAR(150) DEFAULT NULL, INDEX IDX_A1A335C26B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit_of_measurement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_DC68A6045E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE body_bmi ADD CONSTRAINT FK_D1B547FD6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE body_fat ADD CONSTRAINT FK_190FCCF46B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE body_fat ADD CONSTRAINT FK_190FCCF4DCBB0C53 FOREIGN KEY (unit) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE body_fat ADD CONSTRAINT FK_190FCCF4EE0DB122 FOREIGN KEY (part_of_day) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE body_weight ADD CONSTRAINT FK_92D1F6476B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE body_weight ADD CONSTRAINT FK_92D1F647DCBB0C53 FOREIGN KEY (unit) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE body_weight ADD CONSTRAINT FK_92D1F647EE0DB122 FOREIGN KEY (part_of_day) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE count_daily_calories ADD CONSTRAINT FK_27AF31BC6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE count_daily_distance ADD CONSTRAINT FK_1E589C3C6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE count_daily_distance ADD CONSTRAINT FK_1E589C3CDCBB0C53 FOREIGN KEY (unit) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE count_daily_elevation ADD CONSTRAINT FK_89F84FB66B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE count_daily_floor ADD CONSTRAINT FK_4DE77C7D6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE count_daily_step ADD CONSTRAINT FK_5862E52E6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE heart_rate ADD CONSTRAINT FK_84A07ED666951C68 FOREIGN KEY (out_of_range_id) REFERENCES heart_rate_out_of_range (id)');
        $this->addSql('ALTER TABLE heart_rate ADD CONSTRAINT FK_84A07ED64AA71F69 FOREIGN KEY (fat_burn_id) REFERENCES heart_rate_fat_burn (id)');
        $this->addSql('ALTER TABLE heart_rate ADD CONSTRAINT FK_84A07ED619536BEF FOREIGN KEY (cardio_id) REFERENCES heart_rate_cardio (id)');
        $this->addSql('ALTER TABLE heart_rate ADD CONSTRAINT FK_84A07ED6F848A361 FOREIGN KEY (peak_id) REFERENCES heart_rate_peak (id)');
        $this->addSql('ALTER TABLE min_daily_fairly ADD CONSTRAINT FK_415CE5B46B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE min_daily_lightly ADD CONSTRAINT FK_DAA1CD006B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE min_daily_sedentary ADD CONSTRAINT FK_94B437886B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE min_daily_very ADD CONSTRAINT FK_FB8F4C6F6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE nutrition_information ADD CONSTRAINT FK_47B586C86B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE nutrition_information ADD CONSTRAINT FK_47B586C8DCBB0C53 FOREIGN KEY (unit) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE sport_activity ADD CONSTRAINT FK_9AD6F6E06B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE sport_activity ADD CONSTRAINT FK_9AD6F6E05F8A7F73 FOREIGN KEY (source) REFERENCES sport_activity_source (id)');
        $this->addSql('ALTER TABLE sport_activity ADD CONSTRAINT FK_9AD6F6E0882F7BA6 FOREIGN KEY (heart_rate_id) REFERENCES heart_rate (id)');
        $this->addSql('ALTER TABLE sport_activity ADD CONSTRAINT FK_9AD6F6E0FFBBFBA9 FOREIGN KEY (activity_level_id) REFERENCES activity_level (id)');
        $this->addSql('ALTER TABLE sport_activity ADD CONSTRAINT FK_9AD6F6E0EE0DB122 FOREIGN KEY (part_of_day) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE sport_activity ADD CONSTRAINT FK_9AD6F6E0629F99BF FOREIGN KEY (sport_track) REFERENCES sport_track (id)');
        $this->addSql('ALTER TABLE sport_track ADD CONSTRAINT FK_629F99BF6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE sport_track ADD CONSTRAINT FK_629F99BFA1A335C2 FOREIGN KEY (tracking_device) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE sport_track_point ADD CONSTRAINT FK_A46715C56870C3F9 FOREIGN KEY (sport_track_id) REFERENCES sport_track (id)');
        $this->addSql('ALTER TABLE third_party_relations ADD CONSTRAINT FK_23BD673D6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE third_party_relations ADD CONSTRAINT FK_23BD673DE19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE tracking_device ADD CONSTRAINT FK_A1A335C26B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sport_activity DROP FOREIGN KEY FK_9AD6F6E0FFBBFBA9');
        $this->addSql('ALTER TABLE sport_activity DROP FOREIGN KEY FK_9AD6F6E0882F7BA6');
        $this->addSql('ALTER TABLE heart_rate DROP FOREIGN KEY FK_84A07ED619536BEF');
        $this->addSql('ALTER TABLE heart_rate DROP FOREIGN KEY FK_84A07ED64AA71F69');
        $this->addSql('ALTER TABLE heart_rate DROP FOREIGN KEY FK_84A07ED666951C68');
        $this->addSql('ALTER TABLE heart_rate DROP FOREIGN KEY FK_84A07ED6F848A361');
        $this->addSql('ALTER TABLE body_fat DROP FOREIGN KEY FK_190FCCF4EE0DB122');
        $this->addSql('ALTER TABLE body_weight DROP FOREIGN KEY FK_92D1F647EE0DB122');
        $this->addSql('ALTER TABLE sport_activity DROP FOREIGN KEY FK_9AD6F6E0EE0DB122');
        $this->addSql('ALTER TABLE body_bmi DROP FOREIGN KEY FK_D1B547FD6B899279');
        $this->addSql('ALTER TABLE body_fat DROP FOREIGN KEY FK_190FCCF46B899279');
        $this->addSql('ALTER TABLE body_weight DROP FOREIGN KEY FK_92D1F6476B899279');
        $this->addSql('ALTER TABLE count_daily_calories DROP FOREIGN KEY FK_27AF31BC6B899279');
        $this->addSql('ALTER TABLE count_daily_distance DROP FOREIGN KEY FK_1E589C3C6B899279');
        $this->addSql('ALTER TABLE count_daily_elevation DROP FOREIGN KEY FK_89F84FB66B899279');
        $this->addSql('ALTER TABLE count_daily_floor DROP FOREIGN KEY FK_4DE77C7D6B899279');
        $this->addSql('ALTER TABLE count_daily_step DROP FOREIGN KEY FK_5862E52E6B899279');
        $this->addSql('ALTER TABLE min_daily_fairly DROP FOREIGN KEY FK_415CE5B46B899279');
        $this->addSql('ALTER TABLE min_daily_lightly DROP FOREIGN KEY FK_DAA1CD006B899279');
        $this->addSql('ALTER TABLE min_daily_sedentary DROP FOREIGN KEY FK_94B437886B899279');
        $this->addSql('ALTER TABLE min_daily_very DROP FOREIGN KEY FK_FB8F4C6F6B899279');
        $this->addSql('ALTER TABLE nutrition_information DROP FOREIGN KEY FK_47B586C86B899279');
        $this->addSql('ALTER TABLE sport_activity DROP FOREIGN KEY FK_9AD6F6E06B899279');
        $this->addSql('ALTER TABLE sport_track DROP FOREIGN KEY FK_629F99BF6B899279');
        $this->addSql('ALTER TABLE third_party_relations DROP FOREIGN KEY FK_23BD673D6B899279');
        $this->addSql('ALTER TABLE tracking_device DROP FOREIGN KEY FK_A1A335C26B899279');
        $this->addSql('ALTER TABLE sport_activity DROP FOREIGN KEY FK_9AD6F6E05F8A7F73');
        $this->addSql('ALTER TABLE sport_activity DROP FOREIGN KEY FK_9AD6F6E0629F99BF');
        $this->addSql('ALTER TABLE sport_track_point DROP FOREIGN KEY FK_A46715C56870C3F9');
        $this->addSql('ALTER TABLE third_party_relations DROP FOREIGN KEY FK_23BD673DE19D9AD2');
        $this->addSql('ALTER TABLE sport_track DROP FOREIGN KEY FK_629F99BFA1A335C2');
        $this->addSql('ALTER TABLE body_fat DROP FOREIGN KEY FK_190FCCF4DCBB0C53');
        $this->addSql('ALTER TABLE body_weight DROP FOREIGN KEY FK_92D1F647DCBB0C53');
        $this->addSql('ALTER TABLE count_daily_distance DROP FOREIGN KEY FK_1E589C3CDCBB0C53');
        $this->addSql('ALTER TABLE nutrition_information DROP FOREIGN KEY FK_47B586C8DCBB0C53');
        $this->addSql('DROP TABLE activity_level');
        $this->addSql('DROP TABLE body_bmi');
        $this->addSql('DROP TABLE body_fat');
        $this->addSql('DROP TABLE body_weight');
        $this->addSql('DROP TABLE count_daily_calories');
        $this->addSql('DROP TABLE count_daily_distance');
        $this->addSql('DROP TABLE count_daily_elevation');
        $this->addSql('DROP TABLE count_daily_floor');
        $this->addSql('DROP TABLE count_daily_step');
        $this->addSql('DROP TABLE heart_rate');
        $this->addSql('DROP TABLE heart_rate_cardio');
        $this->addSql('DROP TABLE heart_rate_fat_burn');
        $this->addSql('DROP TABLE heart_rate_out_of_range');
        $this->addSql('DROP TABLE heart_rate_peak');
        $this->addSql('DROP TABLE min_daily_fairly');
        $this->addSql('DROP TABLE min_daily_lightly');
        $this->addSql('DROP TABLE min_daily_sedentary');
        $this->addSql('DROP TABLE min_daily_very');
        $this->addSql('DROP TABLE nutrition_information');
        $this->addSql('DROP TABLE part_of_day');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE sport_activity');
        $this->addSql('DROP TABLE sport_activity_source');
        $this->addSql('DROP TABLE sport_track');
        $this->addSql('DROP TABLE sport_track_point');
        $this->addSql('DROP TABLE third_party_relations');
        $this->addSql('DROP TABLE third_party_service');
        $this->addSql('DROP TABLE tracking_device');
        $this->addSql('DROP TABLE unit_of_measurement');
    }
}
