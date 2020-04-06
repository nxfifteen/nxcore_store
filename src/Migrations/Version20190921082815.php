<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */
/** @noinspection DuplicatedCode */

/** @noinspection SqlResolve */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpUnused */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190921082815 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return '';
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE api_access_log (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, third_party_service_id INT NOT NULL, entity VARCHAR(255) NOT NULL, last_retrieved DATETIME NOT NULL, last_pulled DATETIME NOT NULL, cooldown DATETIME DEFAULT NULL, INDEX IDX_28D549016B899279 (patient_id), INDEX IDX_28D54901EBDDCD21 (third_party_service_id), UNIQUE INDEX EntityPulled (patient_id, third_party_service_id, entity), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE body_composition (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, part_of_day_id INT NOT NULL, tracking_device_id INT NOT NULL, skeletal_muscle DOUBLE PRECISION DEFAULT NULL, muscle_mass DOUBLE PRECISION DEFAULT NULL, basal_metabolic_rate DOUBLE PRECISION DEFAULT NULL, skeletal_muscle_mass DOUBLE PRECISION DEFAULT NULL, total_body_water DOUBLE PRECISION DEFAULT NULL, remote_id VARCHAR(255) NOT NULL, date_time DATETIME NOT NULL, INDEX IDX_C5EB24476B899279 (patient_id), INDEX IDX_C5EB2447DEE7B414 (part_of_day_id), INDEX IDX_C5EB24471984B75B (tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE body_fat (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, unit_of_measurement_id INT NOT NULL, part_of_day_id INT DEFAULT NULL, patient_goal_id INT NOT NULL, measurement DOUBLE PRECISION NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, fat_free_mass DOUBLE PRECISION DEFAULT NULL, fat_free DOUBLE PRECISION DEFAULT NULL, body_fat_mass DOUBLE PRECISION DEFAULT NULL, INDEX IDX_190FCCF46B899279 (patient_id), INDEX IDX_190FCCF41984B75B (tracking_device_id), INDEX IDX_190FCCF4DA7C9F35 (unit_of_measurement_id), INDEX IDX_190FCCF4DEE7B414 (part_of_day_id), INDEX IDX_190FCCF43B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE body_weight (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, unit_of_measurement_id INT NOT NULL, part_of_day_id INT DEFAULT NULL, patient_goal_id INT NOT NULL, measurement DOUBLE PRECISION NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, INDEX IDX_92D1F6476B899279 (patient_id), INDEX IDX_92D1F6471984B75B (tracking_device_id), INDEX IDX_92D1F647DA7C9F35 (unit_of_measurement_id), INDEX IDX_92D1F647DEE7B414 (part_of_day_id), INDEX IDX_92D1F6473B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consume_caffeine (id INT AUTO_INCREMENT NOT NULL, unit_of_measurement_id INT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, service_id INT NOT NULL, part_of_day_id INT NOT NULL, patient_goal_id INT DEFAULT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, measurement DOUBLE PRECISION NOT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_2152F836DA7C9F35 (unit_of_measurement_id), INDEX IDX_2152F8366B899279 (patient_id), INDEX IDX_2152F8361984B75B (tracking_device_id), INDEX IDX_2152F836ED5CA9E6 (service_id), INDEX IDX_2152F836DEE7B414 (part_of_day_id), INDEX IDX_2152F8363B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consume_water (id INT AUTO_INCREMENT NOT NULL, unit_of_measurement_id INT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, service_id INT NOT NULL, part_of_day_id INT NOT NULL, patient_goal_id INT DEFAULT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, measurement DOUBLE PRECISION NOT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_11CA8999DA7C9F35 (unit_of_measurement_id), INDEX IDX_11CA89996B899279 (patient_id), INDEX IDX_11CA89991984B75B (tracking_device_id), INDEX IDX_11CA8999ED5CA9E6 (service_id), INDEX IDX_11CA8999DEE7B414 (part_of_day_id), INDEX IDX_11CA89993B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, part_of_day_id INT NOT NULL, exercise_type_id INT NOT NULL, date_time_start DATETIME NOT NULL, date_time_end DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, duration INT NOT NULL, INDEX IDX_AEDAD51C6B899279 (patient_id), INDEX IDX_AEDAD51C1984B75B (tracking_device_id), INDEX IDX_AEDAD51CDEE7B414 (part_of_day_id), INDEX IDX_AEDAD51C1F597BD6 (exercise_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_summary (id INT AUTO_INCREMENT NOT NULL, exercise_id INT NOT NULL, altitude_gain DOUBLE PRECISION DEFAULT NULL, altitude_loss DOUBLE PRECISION DEFAULT NULL, altitude_max DOUBLE PRECISION DEFAULT NULL, altitude_min DOUBLE PRECISION DEFAULT NULL, cadence_max DOUBLE PRECISION DEFAULT NULL, cadence_mean DOUBLE PRECISION DEFAULT NULL, cadence_min DOUBLE PRECISION DEFAULT NULL, calorie DOUBLE PRECISION DEFAULT NULL, distance_incline DOUBLE PRECISION DEFAULT NULL, distance_decline DOUBLE PRECISION DEFAULT NULL, distance DOUBLE PRECISION DEFAULT NULL, speed_max DOUBLE PRECISION DEFAULT NULL, speed_mean DOUBLE PRECISION DEFAULT NULL, heart_rate_max DOUBLE PRECISION DEFAULT NULL, heart_rate_mean DOUBLE PRECISION DEFAULT NULL, heart_rate_min DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_143D9126E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_track (id INT AUTO_INCREMENT NOT NULL, exercise_id INT NOT NULL, time_stamp BIGINT NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, altitude DOUBLE PRECISION DEFAULT NULL, INDEX IDX_F3D50F64E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_calories_daily_summary (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, patient_goal_id INT NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_53EEB7016B899279 (patient_id), INDEX IDX_53EEB7011984B75B (tracking_device_id), INDEX IDX_53EEB7013B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_distance_daily_summary (id INT AUTO_INCREMENT NOT NULL, unit_of_measurement_id INT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, patient_goal_id INT NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_E797247DDA7C9F35 (unit_of_measurement_id), INDEX IDX_E797247D6B899279 (patient_id), INDEX IDX_E797247D1984B75B (tracking_device_id), INDEX IDX_E797247D3B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_floors_intra_day (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value INT NOT NULL, INDEX IDX_60A6DB356B899279 (patient_id), INDEX IDX_60A6DB351984B75B (tracking_device_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_steps_daily_summary (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, patient_goal_id INT NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value INT NOT NULL, INDEX IDX_DE1CE7DF6B899279 (patient_id), INDEX IDX_DE1CE7DF1984B75B (tracking_device_id), INDEX IDX_DE1CE7DF3B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_steps_intra_day (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value INT NOT NULL, hour INT NOT NULL, duration INT DEFAULT NULL, INDEX IDX_D8852F156B899279 (patient_id), INDEX IDX_D8852F151984B75B (tracking_device_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food_database (id INT AUTO_INCREMENT NOT NULL, serving_unit_id INT DEFAULT NULL, service_id INT NOT NULL, provider_id VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, calorie DOUBLE PRECISION DEFAULT NULL, serving_amount DOUBLE PRECISION DEFAULT NULL, total_fat DOUBLE PRECISION DEFAULT NULL, saturated_fat DOUBLE PRECISION DEFAULT NULL, carbohydrate DOUBLE PRECISION DEFAULT NULL, dietary_fiber DOUBLE PRECISION DEFAULT NULL, sugar DOUBLE PRECISION DEFAULT NULL, protein DOUBLE PRECISION DEFAULT NULL, serving_description VARCHAR(255) DEFAULT NULL, serving_number_default DOUBLE PRECISION DEFAULT NULL, remote_ids JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_E11E216CEC3181A7 (serving_unit_id), INDEX IDX_E11E216CED5CA9E6 (service_id), UNIQUE INDEX DeviceRemote (provider_id, service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food_diary (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, food_item_id INT NOT NULL, unit_id INT NOT NULL, meal_id INT NOT NULL, remote_id VARCHAR(255) NOT NULL, date_time DATETIME NOT NULL, amount DOUBLE PRECISION NOT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_601A28086B899279 (patient_id), INDEX IDX_601A28081984B75B (tracking_device_id), INDEX IDX_601A28085DF08E66 (food_item_id), INDEX IDX_601A2808F8BD700D (unit_id), INDEX IDX_601A2808639666D6 (meal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food_meals (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food_nutrition (id INT AUTO_INCREMENT NOT NULL, meal_id INT NOT NULL, tracking_device_id INT NOT NULL, patient_id INT NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, calorie DOUBLE PRECISION DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, total_fat DOUBLE PRECISION DEFAULT NULL, saturated_fat DOUBLE PRECISION DEFAULT NULL, polysaturated_fat DOUBLE PRECISION DEFAULT NULL, monosaturated_fat DOUBLE PRECISION DEFAULT NULL, trans_fat DOUBLE PRECISION DEFAULT NULL, dietary_fiber DOUBLE PRECISION DEFAULT NULL, sugar DOUBLE PRECISION DEFAULT NULL, protein DOUBLE PRECISION DEFAULT NULL, cholesterol DOUBLE PRECISION DEFAULT NULL, sodium DOUBLE PRECISION DEFAULT NULL, potassium DOUBLE PRECISION DEFAULT NULL, vit_a DOUBLE PRECISION DEFAULT NULL, vit_c DOUBLE PRECISION DEFAULT NULL, calcium DOUBLE PRECISION DEFAULT NULL, iron DOUBLE PRECISION DEFAULT NULL, carbohydrate DOUBLE PRECISION DEFAULT NULL, date_time DATETIME NOT NULL, INDEX IDX_2F390D6A639666D6 (meal_id), INDEX IDX_2F390D6A1984B75B (tracking_device_id), INDEX IDX_2F390D6A6B899279 (patient_id), UNIQUE INDEX DeviceRemote (remote_id, meal_id, patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE part_of_day (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, sur_name VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, ui_settings JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', rpg_factor DOUBLE PRECISION DEFAULT NULL, first_run TINYINT(1) DEFAULT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1ADAD7EBD17F50A6 (uuid), UNIQUE INDEX UNIQ_1ADAD7EB7BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_credentials (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, service_id INT NOT NULL, token LONGTEXT NOT NULL, refresh_token VARCHAR(255) DEFAULT NULL, expires DATETIME DEFAULT NULL, INDEX IDX_CA0617416B899279 (patient_id), INDEX IDX_CA061741ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_goals (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, unit_of_measurement_id INT DEFAULT NULL, entity VARCHAR(255) NOT NULL, goal DOUBLE PRECISION NOT NULL, date_set DATETIME NOT NULL, INDEX IDX_5212C5E06B899279 (patient_id), INDEX IDX_5212C5E0DA7C9F35 (unit_of_measurement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_challenge_friends (id INT AUTO_INCREMENT NOT NULL, challenger_id INT NOT NULL, challenged_id INT NOT NULL, target INT NOT NULL, invite_date DATETIME NOT NULL, start_date DATETIME DEFAULT NULL, duration INT NOT NULL, criteria VARCHAR(255) NOT NULL, outcome INT DEFAULT NULL, end_date DATETIME DEFAULT NULL, challenger_sum INT DEFAULT NULL, challenged_sum INT DEFAULT NULL, challenger_details LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', challenged_details LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_5BDB23D2D521FDF (challenger_id), INDEX IDX_5BDB23D5820179C (challenged_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_milestones (id INT AUTO_INCREMENT NOT NULL, category VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, msg_less VARCHAR(255) DEFAULT NULL, msg_more VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_rewards (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, xp DOUBLE PRECISION DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, text VARCHAR(255) NOT NULL, text_long LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_rewards_awarded (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, reward_id INT NOT NULL, datetime DATETIME NOT NULL, INDEX IDX_A069EAE66B899279 (patient_id), INDEX IDX_A069EAE6E466ACA1 (reward_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_xp (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, datetime DATETIME NOT NULL, reason VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_7808C9536B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sync_queue (id INT AUTO_INCREMENT NOT NULL, credentials_id INT NOT NULL, service_id INT NOT NULL, endpoint VARCHAR(255) NOT NULL, datetime DATETIME NOT NULL, INDEX IDX_99CCF5CE41E8B2E5 (credentials_id), INDEX IDX_99CCF5CEED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE third_party_service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tracking_device (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, service_id INT NOT NULL, name VARCHAR(150) DEFAULT NULL, comment VARCHAR(200) DEFAULT NULL, battery INT DEFAULT NULL, last_synced DATETIME DEFAULT NULL, remote_id VARCHAR(255) DEFAULT NULL, type VARCHAR(150) DEFAULT NULL, manufacturer VARCHAR(255) DEFAULT NULL, model VARCHAR(255) DEFAULT NULL, INDEX IDX_A1A335C26B899279 (patient_id), INDEX IDX_A1A335C2ED5CA9E6 (service_id), UNIQUE INDEX DeviceService (remote_id, service_id, patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit_of_measurement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_access_log ADD CONSTRAINT FK_28D549016B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE api_access_log ADD CONSTRAINT FK_28D54901EBDDCD21 FOREIGN KEY (third_party_service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE body_composition ADD CONSTRAINT FK_C5EB24476B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE body_composition ADD CONSTRAINT FK_C5EB2447DEE7B414 FOREIGN KEY (part_of_day_id) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE body_composition ADD CONSTRAINT FK_C5EB24471984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE body_fat ADD CONSTRAINT FK_190FCCF46B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE body_fat ADD CONSTRAINT FK_190FCCF41984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE body_fat ADD CONSTRAINT FK_190FCCF4DA7C9F35 FOREIGN KEY (unit_of_measurement_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE body_fat ADD CONSTRAINT FK_190FCCF4DEE7B414 FOREIGN KEY (part_of_day_id) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE body_fat ADD CONSTRAINT FK_190FCCF43B1E4F15 FOREIGN KEY (patient_goal_id) REFERENCES patient_goals (id)');
        $this->addSql('ALTER TABLE body_weight ADD CONSTRAINT FK_92D1F6476B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE body_weight ADD CONSTRAINT FK_92D1F6471984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE body_weight ADD CONSTRAINT FK_92D1F647DA7C9F35 FOREIGN KEY (unit_of_measurement_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE body_weight ADD CONSTRAINT FK_92D1F647DEE7B414 FOREIGN KEY (part_of_day_id) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE body_weight ADD CONSTRAINT FK_92D1F6473B1E4F15 FOREIGN KEY (patient_goal_id) REFERENCES patient_goals (id)');
        $this->addSql('ALTER TABLE consume_caffeine ADD CONSTRAINT FK_2152F836DA7C9F35 FOREIGN KEY (unit_of_measurement_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE consume_caffeine ADD CONSTRAINT FK_2152F8366B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE consume_caffeine ADD CONSTRAINT FK_2152F8361984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE consume_caffeine ADD CONSTRAINT FK_2152F836ED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE consume_caffeine ADD CONSTRAINT FK_2152F836DEE7B414 FOREIGN KEY (part_of_day_id) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE consume_caffeine ADD CONSTRAINT FK_2152F8363B1E4F15 FOREIGN KEY (patient_goal_id) REFERENCES patient_goals (id)');
        $this->addSql('ALTER TABLE consume_water ADD CONSTRAINT FK_11CA8999DA7C9F35 FOREIGN KEY (unit_of_measurement_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE consume_water ADD CONSTRAINT FK_11CA89996B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE consume_water ADD CONSTRAINT FK_11CA89991984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE consume_water ADD CONSTRAINT FK_11CA8999ED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE consume_water ADD CONSTRAINT FK_11CA8999DEE7B414 FOREIGN KEY (part_of_day_id) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE consume_water ADD CONSTRAINT FK_11CA89993B1E4F15 FOREIGN KEY (patient_goal_id) REFERENCES patient_goals (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C1984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51CDEE7B414 FOREIGN KEY (part_of_day_id) REFERENCES part_of_day (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C1F597BD6 FOREIGN KEY (exercise_type_id) REFERENCES exercise_type (id)');
        $this->addSql('ALTER TABLE exercise_summary ADD CONSTRAINT FK_143D9126E934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id)');
        $this->addSql('ALTER TABLE exercise_track ADD CONSTRAINT FK_F3D50F64E934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id)');
        $this->addSql('ALTER TABLE fit_calories_daily_summary ADD CONSTRAINT FK_53EEB7016B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE fit_calories_daily_summary ADD CONSTRAINT FK_53EEB7011984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE fit_calories_daily_summary ADD CONSTRAINT FK_53EEB7013B1E4F15 FOREIGN KEY (patient_goal_id) REFERENCES patient_goals (id)');
        $this->addSql('ALTER TABLE fit_distance_daily_summary ADD CONSTRAINT FK_E797247DDA7C9F35 FOREIGN KEY (unit_of_measurement_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE fit_distance_daily_summary ADD CONSTRAINT FK_E797247D6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE fit_distance_daily_summary ADD CONSTRAINT FK_E797247D1984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE fit_distance_daily_summary ADD CONSTRAINT FK_E797247D3B1E4F15 FOREIGN KEY (patient_goal_id) REFERENCES patient_goals (id)');
        $this->addSql('ALTER TABLE fit_floors_intra_day ADD CONSTRAINT FK_60A6DB356B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE fit_floors_intra_day ADD CONSTRAINT FK_60A6DB351984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE fit_steps_daily_summary ADD CONSTRAINT FK_DE1CE7DF6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE fit_steps_daily_summary ADD CONSTRAINT FK_DE1CE7DF1984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE fit_steps_daily_summary ADD CONSTRAINT FK_DE1CE7DF3B1E4F15 FOREIGN KEY (patient_goal_id) REFERENCES patient_goals (id)');
        $this->addSql('ALTER TABLE fit_steps_intra_day ADD CONSTRAINT FK_D8852F156B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE fit_steps_intra_day ADD CONSTRAINT FK_D8852F151984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE food_database ADD CONSTRAINT FK_E11E216CEC3181A7 FOREIGN KEY (serving_unit_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE food_database ADD CONSTRAINT FK_E11E216CED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE food_diary ADD CONSTRAINT FK_601A28086B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE food_diary ADD CONSTRAINT FK_601A28081984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE food_diary ADD CONSTRAINT FK_601A28085DF08E66 FOREIGN KEY (food_item_id) REFERENCES food_database (id)');
        $this->addSql('ALTER TABLE food_diary ADD CONSTRAINT FK_601A2808F8BD700D FOREIGN KEY (unit_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE food_diary ADD CONSTRAINT FK_601A2808639666D6 FOREIGN KEY (meal_id) REFERENCES food_meals (id)');
        $this->addSql('ALTER TABLE food_nutrition ADD CONSTRAINT FK_2F390D6A639666D6 FOREIGN KEY (meal_id) REFERENCES food_meals (id)');
        $this->addSql('ALTER TABLE food_nutrition ADD CONSTRAINT FK_2F390D6A1984B75B FOREIGN KEY (tracking_device_id) REFERENCES tracking_device (id)');
        $this->addSql('ALTER TABLE food_nutrition ADD CONSTRAINT FK_2F390D6A6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE patient_credentials ADD CONSTRAINT FK_CA0617416B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE patient_credentials ADD CONSTRAINT FK_CA061741ED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE patient_goals ADD CONSTRAINT FK_5212C5E06B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE patient_goals ADD CONSTRAINT FK_5212C5E0DA7C9F35 FOREIGN KEY (unit_of_measurement_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE rpg_challenge_friends ADD CONSTRAINT FK_5BDB23D2D521FDF FOREIGN KEY (challenger_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE rpg_challenge_friends ADD CONSTRAINT FK_5BDB23D5820179C FOREIGN KEY (challenged_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE rpg_rewards_awarded ADD CONSTRAINT FK_A069EAE66B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE rpg_rewards_awarded ADD CONSTRAINT FK_A069EAE6E466ACA1 FOREIGN KEY (reward_id) REFERENCES rpg_rewards (id)');
        $this->addSql('ALTER TABLE rpg_xp ADD CONSTRAINT FK_7808C9536B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE sync_queue ADD CONSTRAINT FK_99CCF5CE41E8B2E5 FOREIGN KEY (credentials_id) REFERENCES patient_credentials (id)');
        $this->addSql('ALTER TABLE sync_queue ADD CONSTRAINT FK_99CCF5CEED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE tracking_device ADD CONSTRAINT FK_A1A335C26B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE tracking_device ADD CONSTRAINT FK_A1A335C2ED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE exercise_summary DROP FOREIGN KEY FK_143D9126E934951A');
        $this->addSql('ALTER TABLE exercise_track DROP FOREIGN KEY FK_F3D50F64E934951A');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C1F597BD6');
        $this->addSql('ALTER TABLE food_diary DROP FOREIGN KEY FK_601A28085DF08E66');
        $this->addSql('ALTER TABLE food_diary DROP FOREIGN KEY FK_601A2808639666D6');
        $this->addSql('ALTER TABLE food_nutrition DROP FOREIGN KEY FK_2F390D6A639666D6');
        $this->addSql('ALTER TABLE body_composition DROP FOREIGN KEY FK_C5EB2447DEE7B414');
        $this->addSql('ALTER TABLE body_fat DROP FOREIGN KEY FK_190FCCF4DEE7B414');
        $this->addSql('ALTER TABLE body_weight DROP FOREIGN KEY FK_92D1F647DEE7B414');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F836DEE7B414');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA8999DEE7B414');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51CDEE7B414');
        $this->addSql('ALTER TABLE api_access_log DROP FOREIGN KEY FK_28D549016B899279');
        $this->addSql('ALTER TABLE body_composition DROP FOREIGN KEY FK_C5EB24476B899279');
        $this->addSql('ALTER TABLE body_fat DROP FOREIGN KEY FK_190FCCF46B899279');
        $this->addSql('ALTER TABLE body_weight DROP FOREIGN KEY FK_92D1F6476B899279');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F8366B899279');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA89996B899279');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C6B899279');
        $this->addSql('ALTER TABLE fit_calories_daily_summary DROP FOREIGN KEY FK_53EEB7016B899279');
        $this->addSql('ALTER TABLE fit_distance_daily_summary DROP FOREIGN KEY FK_E797247D6B899279');
        $this->addSql('ALTER TABLE fit_floors_intra_day DROP FOREIGN KEY FK_60A6DB356B899279');
        $this->addSql('ALTER TABLE fit_steps_daily_summary DROP FOREIGN KEY FK_DE1CE7DF6B899279');
        $this->addSql('ALTER TABLE fit_steps_intra_day DROP FOREIGN KEY FK_D8852F156B899279');
        $this->addSql('ALTER TABLE food_diary DROP FOREIGN KEY FK_601A28086B899279');
        $this->addSql('ALTER TABLE food_nutrition DROP FOREIGN KEY FK_2F390D6A6B899279');
        $this->addSql('ALTER TABLE patient_credentials DROP FOREIGN KEY FK_CA0617416B899279');
        $this->addSql('ALTER TABLE patient_goals DROP FOREIGN KEY FK_5212C5E06B899279');
        $this->addSql('ALTER TABLE rpg_challenge_friends DROP FOREIGN KEY FK_5BDB23D2D521FDF');
        $this->addSql('ALTER TABLE rpg_challenge_friends DROP FOREIGN KEY FK_5BDB23D5820179C');
        $this->addSql('ALTER TABLE rpg_rewards_awarded DROP FOREIGN KEY FK_A069EAE66B899279');
        $this->addSql('ALTER TABLE rpg_xp DROP FOREIGN KEY FK_7808C9536B899279');
        $this->addSql('ALTER TABLE tracking_device DROP FOREIGN KEY FK_A1A335C26B899279');
        $this->addSql('ALTER TABLE sync_queue DROP FOREIGN KEY FK_99CCF5CE41E8B2E5');
        $this->addSql('ALTER TABLE body_fat DROP FOREIGN KEY FK_190FCCF43B1E4F15');
        $this->addSql('ALTER TABLE body_weight DROP FOREIGN KEY FK_92D1F6473B1E4F15');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F8363B1E4F15');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA89993B1E4F15');
        $this->addSql('ALTER TABLE fit_calories_daily_summary DROP FOREIGN KEY FK_53EEB7013B1E4F15');
        $this->addSql('ALTER TABLE fit_distance_daily_summary DROP FOREIGN KEY FK_E797247D3B1E4F15');
        $this->addSql('ALTER TABLE fit_steps_daily_summary DROP FOREIGN KEY FK_DE1CE7DF3B1E4F15');
        $this->addSql('ALTER TABLE rpg_rewards_awarded DROP FOREIGN KEY FK_A069EAE6E466ACA1');
        $this->addSql('ALTER TABLE api_access_log DROP FOREIGN KEY FK_28D54901EBDDCD21');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F836ED5CA9E6');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA8999ED5CA9E6');
        $this->addSql('ALTER TABLE food_database DROP FOREIGN KEY FK_E11E216CED5CA9E6');
        $this->addSql('ALTER TABLE patient_credentials DROP FOREIGN KEY FK_CA061741ED5CA9E6');
        $this->addSql('ALTER TABLE sync_queue DROP FOREIGN KEY FK_99CCF5CEED5CA9E6');
        $this->addSql('ALTER TABLE tracking_device DROP FOREIGN KEY FK_A1A335C2ED5CA9E6');
        $this->addSql('ALTER TABLE body_composition DROP FOREIGN KEY FK_C5EB24471984B75B');
        $this->addSql('ALTER TABLE body_fat DROP FOREIGN KEY FK_190FCCF41984B75B');
        $this->addSql('ALTER TABLE body_weight DROP FOREIGN KEY FK_92D1F6471984B75B');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F8361984B75B');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA89991984B75B');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C1984B75B');
        $this->addSql('ALTER TABLE fit_calories_daily_summary DROP FOREIGN KEY FK_53EEB7011984B75B');
        $this->addSql('ALTER TABLE fit_distance_daily_summary DROP FOREIGN KEY FK_E797247D1984B75B');
        $this->addSql('ALTER TABLE fit_floors_intra_day DROP FOREIGN KEY FK_60A6DB351984B75B');
        $this->addSql('ALTER TABLE fit_steps_daily_summary DROP FOREIGN KEY FK_DE1CE7DF1984B75B');
        $this->addSql('ALTER TABLE fit_steps_intra_day DROP FOREIGN KEY FK_D8852F151984B75B');
        $this->addSql('ALTER TABLE food_diary DROP FOREIGN KEY FK_601A28081984B75B');
        $this->addSql('ALTER TABLE food_nutrition DROP FOREIGN KEY FK_2F390D6A1984B75B');
        $this->addSql('ALTER TABLE body_fat DROP FOREIGN KEY FK_190FCCF4DA7C9F35');
        $this->addSql('ALTER TABLE body_weight DROP FOREIGN KEY FK_92D1F647DA7C9F35');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F836DA7C9F35');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA8999DA7C9F35');
        $this->addSql('ALTER TABLE fit_distance_daily_summary DROP FOREIGN KEY FK_E797247DDA7C9F35');
        $this->addSql('ALTER TABLE food_database DROP FOREIGN KEY FK_E11E216CEC3181A7');
        $this->addSql('ALTER TABLE food_diary DROP FOREIGN KEY FK_601A2808F8BD700D');
        $this->addSql('ALTER TABLE patient_goals DROP FOREIGN KEY FK_5212C5E0DA7C9F35');
        $this->addSql('DROP TABLE api_access_log');
        $this->addSql('DROP TABLE body_composition');
        $this->addSql('DROP TABLE body_fat');
        $this->addSql('DROP TABLE body_weight');
        $this->addSql('DROP TABLE consume_caffeine');
        $this->addSql('DROP TABLE consume_water');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('DROP TABLE exercise_summary');
        $this->addSql('DROP TABLE exercise_track');
        $this->addSql('DROP TABLE exercise_type');
        $this->addSql('DROP TABLE fit_calories_daily_summary');
        $this->addSql('DROP TABLE fit_distance_daily_summary');
        $this->addSql('DROP TABLE fit_floors_intra_day');
        $this->addSql('DROP TABLE fit_steps_daily_summary');
        $this->addSql('DROP TABLE fit_steps_intra_day');
        $this->addSql('DROP TABLE food_database');
        $this->addSql('DROP TABLE food_diary');
        $this->addSql('DROP TABLE food_meals');
        $this->addSql('DROP TABLE food_nutrition');
        $this->addSql('DROP TABLE part_of_day');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE patient_credentials');
        $this->addSql('DROP TABLE patient_goals');
        $this->addSql('DROP TABLE rpg_challenge_friends');
        $this->addSql('DROP TABLE rpg_milestones');
        $this->addSql('DROP TABLE rpg_rewards');
        $this->addSql('DROP TABLE rpg_rewards_awarded');
        $this->addSql('DROP TABLE rpg_xp');
        $this->addSql('DROP TABLE sync_queue');
        $this->addSql('DROP TABLE third_party_service');
        $this->addSql('DROP TABLE tracking_device');
        $this->addSql('DROP TABLE unit_of_measurement');
    }
}
