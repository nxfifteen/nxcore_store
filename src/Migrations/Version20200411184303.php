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
final class Version20200411184303 extends AbstractMigration
{
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE uploaded_file DROP FOREIGN KEY FK_B40DF75D460F904B');
        $this->addSql('ALTER TABLE workout_exercise DROP FOREIGN KEY FK_76AB38AA460F904B');
        $this->addSql('ALTER TABLE exercise_summary DROP FOREIGN KEY FK_143D9126E934951A');
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
        $this->addSql('ALTER TABLE patient_device DROP FOREIGN KEY FK_2615F746B899279');
        $this->addSql('ALTER TABLE patient_friends DROP FOREIGN KEY FK_7C8D2582A1A48FB8');
        $this->addSql('ALTER TABLE patient_friends DROP FOREIGN KEY FK_7C8D2582B3112056');
        $this->addSql('ALTER TABLE patient_goals DROP FOREIGN KEY FK_5212C5E06B899279');
        $this->addSql('ALTER TABLE patient_membership DROP FOREIGN KEY FK_303F746A6B899279');
        $this->addSql('ALTER TABLE patient_settings DROP FOREIGN KEY FK_D2C0F8606B899279');
        $this->addSql('ALTER TABLE rpg_challenge_friends DROP FOREIGN KEY FK_5BDB23D2D521FDF');
        $this->addSql('ALTER TABLE rpg_challenge_friends DROP FOREIGN KEY FK_5BDB23D5820179C');
        $this->addSql('ALTER TABLE rpg_challenge_global_patient DROP FOREIGN KEY FK_AA07E8636B899279');
        $this->addSql('ALTER TABLE rpg_rewards_awarded DROP FOREIGN KEY FK_A069EAE66B899279');
        $this->addSql('ALTER TABLE rpg_xp DROP FOREIGN KEY FK_7808C9536B899279');
        $this->addSql('ALTER TABLE site_news DROP FOREIGN KEY FK_264924A96B899279');
        $this->addSql('ALTER TABLE tracking_device DROP FOREIGN KEY FK_A1A335C26B899279');
        $this->addSql('ALTER TABLE sync_queue DROP FOREIGN KEY FK_99CCF5CE41E8B2E5');
        $this->addSql('ALTER TABLE body_fat DROP FOREIGN KEY FK_190FCCF43B1E4F15');
        $this->addSql('ALTER TABLE body_weight DROP FOREIGN KEY FK_92D1F6473B1E4F15');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F8363B1E4F15');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA89993B1E4F15');
        $this->addSql('ALTER TABLE fit_calories_daily_summary DROP FOREIGN KEY FK_53EEB7013B1E4F15');
        $this->addSql('ALTER TABLE fit_distance_daily_summary DROP FOREIGN KEY FK_E797247D3B1E4F15');
        $this->addSql('ALTER TABLE fit_steps_daily_summary DROP FOREIGN KEY FK_DE1CE7DF3B1E4F15');
        $this->addSql('ALTER TABLE rpg_challenge_global DROP FOREIGN KEY FK_F64797090B01EF');
        $this->addSql('ALTER TABLE rpg_challenge_global_patient DROP FOREIGN KEY FK_AA07E86398A21AC6');
        $this->addSql('ALTER TABLE rpg_rewards DROP FOREIGN KEY FK_FBCA13084402854A');
        $this->addSql('ALTER TABLE rpg_challenge_global DROP FOREIGN KEY FK_F647970E466ACA1');
        $this->addSql('ALTER TABLE rpg_rewards_awarded DROP FOREIGN KEY FK_A069EAE6E466ACA1');
        $this->addSql('ALTER TABLE api_access_log DROP FOREIGN KEY FK_28D54901EBDDCD21');
        $this->addSql('ALTER TABLE consume_caffeine DROP FOREIGN KEY FK_2152F836ED5CA9E6');
        $this->addSql('ALTER TABLE consume_water DROP FOREIGN KEY FK_11CA8999ED5CA9E6');
        $this->addSql('ALTER TABLE food_database DROP FOREIGN KEY FK_E11E216CED5CA9E6');
        $this->addSql('ALTER TABLE patient_credentials DROP FOREIGN KEY FK_CA061741ED5CA9E6');
        $this->addSql('ALTER TABLE patient_settings DROP FOREIGN KEY FK_D2C0F860ED5CA9E6');
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
        $this->addSql('ALTER TABLE rpg_challenge_global DROP FOREIGN KEY FK_F647970DA7C9F35');
        $this->addSql('ALTER TABLE workout_exercise_workout_categories DROP FOREIGN KEY FK_FE7786CD5523D96');
        $this->addSql('ALTER TABLE workout_exercise DROP FOREIGN KEY FK_76AB38AA517FE9FE');
        $this->addSql('ALTER TABLE uploaded_file DROP FOREIGN KEY FK_B40DF75DE934951A');
        $this->addSql('ALTER TABLE workout_exercise_workout_categories DROP FOREIGN KEY FK_FE7786CDE435DB6B');
        $this->addSql('ALTER TABLE workout_muscle_relation DROP FOREIGN KEY FK_56B0B011E934951A');
        $this->addSql('ALTER TABLE workout_muscle_relation DROP FOREIGN KEY FK_56B0B011354FDBB4');
        $this->addSql('DROP TABLE api_access_log');
        $this->addSql('DROP TABLE body_composition');
        $this->addSql('DROP TABLE body_fat');
        $this->addSql('DROP TABLE body_weight');
        $this->addSql('DROP TABLE consume_caffeine');
        $this->addSql('DROP TABLE consume_water');
        $this->addSql('DROP TABLE contribution_license');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('DROP TABLE exercise_summary');
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
        $this->addSql('DROP TABLE patient_device');
        $this->addSql('DROP TABLE patient_friends');
        $this->addSql('DROP TABLE patient_goals');
        $this->addSql('DROP TABLE patient_membership');
        $this->addSql('DROP TABLE patient_settings');
        $this->addSql('DROP TABLE rpg_challenge_friends');
        $this->addSql('DROP TABLE rpg_challenge_global');
        $this->addSql('DROP TABLE rpg_challenge_global_patient');
        $this->addSql('DROP TABLE rpg_indicator');
        $this->addSql('DROP TABLE rpg_milestones');
        $this->addSql('DROP TABLE rpg_rewards');
        $this->addSql('DROP TABLE rpg_rewards_awarded');
        $this->addSql('DROP TABLE rpg_xp');
        $this->addSql('DROP TABLE site_nav_item');
        $this->addSql('DROP TABLE site_news');
        $this->addSql('DROP TABLE sync_queue');
        $this->addSql('DROP TABLE third_party_service');
        $this->addSql('DROP TABLE tracking_device');
        $this->addSql('DROP TABLE unit_of_measurement');
        $this->addSql('DROP TABLE uploaded_file');
        $this->addSql('DROP TABLE workout_categories');
        $this->addSql('DROP TABLE workout_equipment');
        $this->addSql('DROP TABLE workout_exercise');
        $this->addSql('DROP TABLE workout_exercise_workout_categories');
        $this->addSql('DROP TABLE workout_muscle');
        $this->addSql('DROP TABLE workout_muscle_relation');
    }

    public function getDescription(): string
    {
        return 'Initial Install. Create all the table and linkings';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE api_access_log (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, third_party_service_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', entity VARCHAR(255) NOT NULL, last_retrieved DATETIME NOT NULL, last_pulled DATETIME NOT NULL, cooldown DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_28D549012B6FCFB2 (guid), INDEX IDX_28D549016B899279 (patient_id), INDEX IDX_28D54901EBDDCD21 (third_party_service_id), UNIQUE INDEX EntityPulled (patient_id, third_party_service_id, entity), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE body_composition (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, part_of_day_id INT NOT NULL, tracking_device_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', skeletal_muscle DOUBLE PRECISION DEFAULT NULL, muscle_mass DOUBLE PRECISION DEFAULT NULL, basal_metabolic_rate DOUBLE PRECISION DEFAULT NULL, skeletal_muscle_mass DOUBLE PRECISION DEFAULT NULL, total_body_water DOUBLE PRECISION DEFAULT NULL, remote_id VARCHAR(255) NOT NULL, date_time DATETIME NOT NULL, UNIQUE INDEX UNIQ_C5EB24472B6FCFB2 (guid), INDEX IDX_C5EB24476B899279 (patient_id), INDEX IDX_C5EB2447DEE7B414 (part_of_day_id), INDEX IDX_C5EB24471984B75B (tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE body_fat (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, unit_of_measurement_id INT NOT NULL, part_of_day_id INT DEFAULT NULL, patient_goal_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', measurement DOUBLE PRECISION NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, fat_free_mass DOUBLE PRECISION DEFAULT NULL, fat_free DOUBLE PRECISION DEFAULT NULL, body_fat_mass DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_190FCCF42B6FCFB2 (guid), INDEX IDX_190FCCF46B899279 (patient_id), INDEX IDX_190FCCF41984B75B (tracking_device_id), INDEX IDX_190FCCF4DA7C9F35 (unit_of_measurement_id), INDEX IDX_190FCCF4DEE7B414 (part_of_day_id), INDEX IDX_190FCCF43B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE body_weight (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, unit_of_measurement_id INT NOT NULL, part_of_day_id INT DEFAULT NULL, patient_goal_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', measurement DOUBLE PRECISION NOT NULL, date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_92D1F6472B6FCFB2 (guid), INDEX IDX_92D1F6476B899279 (patient_id), INDEX IDX_92D1F6471984B75B (tracking_device_id), INDEX IDX_92D1F647DA7C9F35 (unit_of_measurement_id), INDEX IDX_92D1F647DEE7B414 (part_of_day_id), INDEX IDX_92D1F6473B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consume_caffeine (id INT AUTO_INCREMENT NOT NULL, unit_of_measurement_id INT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, service_id INT NOT NULL, part_of_day_id INT NOT NULL, patient_goal_id INT DEFAULT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, measurement DOUBLE PRECISION NOT NULL, comment VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_2152F8362B6FCFB2 (guid), INDEX IDX_2152F836DA7C9F35 (unit_of_measurement_id), INDEX IDX_2152F8366B899279 (patient_id), INDEX IDX_2152F8361984B75B (tracking_device_id), INDEX IDX_2152F836ED5CA9E6 (service_id), INDEX IDX_2152F836DEE7B414 (part_of_day_id), INDEX IDX_2152F8363B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consume_water (id INT AUTO_INCREMENT NOT NULL, unit_of_measurement_id INT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, service_id INT NOT NULL, part_of_day_id INT NOT NULL, patient_goal_id INT DEFAULT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, measurement DOUBLE PRECISION NOT NULL, comment VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_11CA89992B6FCFB2 (guid), INDEX IDX_11CA8999DA7C9F35 (unit_of_measurement_id), INDEX IDX_11CA89996B899279 (patient_id), INDEX IDX_11CA89991984B75B (tracking_device_id), INDEX IDX_11CA8999ED5CA9E6 (service_id), INDEX IDX_11CA8999DEE7B414 (part_of_day_id), INDEX IDX_11CA89993B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contribution_license (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, link VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_164984D82B6FCFB2 (guid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, part_of_day_id INT NOT NULL, exercise_type_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date_time_start DATETIME NOT NULL, date_time_end DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, duration INT NOT NULL, steps INT DEFAULT NULL, live_data_blob LONGBLOB DEFAULT NULL, location_data_blob LONGBLOB DEFAULT NULL, UNIQUE INDEX UNIQ_AEDAD51C2B6FCFB2 (guid), INDEX IDX_AEDAD51C6B899279 (patient_id), INDEX IDX_AEDAD51C1984B75B (tracking_device_id), INDEX IDX_AEDAD51CDEE7B414 (part_of_day_id), INDEX IDX_AEDAD51C1F597BD6 (exercise_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_summary (id INT AUTO_INCREMENT NOT NULL, exercise_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', altitude_gain DOUBLE PRECISION DEFAULT NULL, altitude_loss DOUBLE PRECISION DEFAULT NULL, altitude_max DOUBLE PRECISION DEFAULT NULL, altitude_min DOUBLE PRECISION DEFAULT NULL, cadence_max DOUBLE PRECISION DEFAULT NULL, cadence_mean DOUBLE PRECISION DEFAULT NULL, cadence_min DOUBLE PRECISION DEFAULT NULL, calorie DOUBLE PRECISION DEFAULT NULL, distance_incline DOUBLE PRECISION DEFAULT NULL, distance_decline DOUBLE PRECISION DEFAULT NULL, distance DOUBLE PRECISION DEFAULT NULL, speed_max DOUBLE PRECISION DEFAULT NULL, speed_mean DOUBLE PRECISION DEFAULT NULL, heart_rate_max DOUBLE PRECISION DEFAULT NULL, heart_rate_mean DOUBLE PRECISION DEFAULT NULL, heart_rate_min DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_143D91262B6FCFB2 (guid), UNIQUE INDEX UNIQ_143D9126E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_type (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, tag VARCHAR(255) DEFAULT NULL, met DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_D5FB359B2B6FCFB2 (guid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_calories_daily_summary (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, patient_goal_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_53EEB7012B6FCFB2 (guid), INDEX IDX_53EEB7016B899279 (patient_id), INDEX IDX_53EEB7011984B75B (tracking_device_id), INDEX IDX_53EEB7013B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_distance_daily_summary (id INT AUTO_INCREMENT NOT NULL, unit_of_measurement_id INT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, patient_goal_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_E797247D2B6FCFB2 (guid), INDEX IDX_E797247DDA7C9F35 (unit_of_measurement_id), INDEX IDX_E797247D6B899279 (patient_id), INDEX IDX_E797247D1984B75B (tracking_device_id), INDEX IDX_E797247D3B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_floors_intra_day (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value INT NOT NULL, UNIQUE INDEX UNIQ_60A6DB352B6FCFB2 (guid), INDEX IDX_60A6DB356B899279 (patient_id), INDEX IDX_60A6DB351984B75B (tracking_device_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_steps_daily_summary (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, patient_goal_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value INT NOT NULL, UNIQUE INDEX UNIQ_DE1CE7DF2B6FCFB2 (guid), INDEX IDX_DE1CE7DF6B899279 (patient_id), INDEX IDX_DE1CE7DF1984B75B (tracking_device_id), INDEX IDX_DE1CE7DF3B1E4F15 (patient_goal_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fit_steps_intra_day (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date_time DATETIME NOT NULL, remote_id VARCHAR(255) DEFAULT NULL, value INT NOT NULL, hour INT NOT NULL, duration INT DEFAULT NULL, UNIQUE INDEX UNIQ_D8852F152B6FCFB2 (guid), INDEX IDX_D8852F156B899279 (patient_id), INDEX IDX_D8852F151984B75B (tracking_device_id), UNIQUE INDEX DeviceRemote (remote_id, tracking_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food_database (id INT AUTO_INCREMENT NOT NULL, serving_unit_id INT DEFAULT NULL, service_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', provider_id VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, calorie DOUBLE PRECISION DEFAULT NULL, serving_amount DOUBLE PRECISION DEFAULT NULL, total_fat DOUBLE PRECISION DEFAULT NULL, saturated_fat DOUBLE PRECISION DEFAULT NULL, carbohydrate DOUBLE PRECISION DEFAULT NULL, dietary_fiber DOUBLE PRECISION DEFAULT NULL, sugar DOUBLE PRECISION DEFAULT NULL, protein DOUBLE PRECISION DEFAULT NULL, serving_description VARCHAR(255) DEFAULT NULL, serving_number_default DOUBLE PRECISION DEFAULT NULL, remote_ids JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX UNIQ_E11E216C2B6FCFB2 (guid), INDEX IDX_E11E216CEC3181A7 (serving_unit_id), INDEX IDX_E11E216CED5CA9E6 (service_id), UNIQUE INDEX DeviceRemote (provider_id, service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food_diary (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, tracking_device_id INT NOT NULL, food_item_id INT NOT NULL, unit_id INT NOT NULL, meal_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', remote_id VARCHAR(255) NOT NULL, date_time DATETIME NOT NULL, amount DOUBLE PRECISION NOT NULL, comment VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_601A28082B6FCFB2 (guid), INDEX IDX_601A28086B899279 (patient_id), INDEX IDX_601A28081984B75B (tracking_device_id), INDEX IDX_601A28085DF08E66 (food_item_id), INDEX IDX_601A2808F8BD700D (unit_id), INDEX IDX_601A2808639666D6 (meal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food_meals (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_134823002B6FCFB2 (guid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food_nutrition (id INT AUTO_INCREMENT NOT NULL, meal_id INT NOT NULL, tracking_device_id INT NOT NULL, patient_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', remote_id VARCHAR(255) DEFAULT NULL, calorie DOUBLE PRECISION DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, total_fat DOUBLE PRECISION DEFAULT NULL, saturated_fat DOUBLE PRECISION DEFAULT NULL, polysaturated_fat DOUBLE PRECISION DEFAULT NULL, monosaturated_fat DOUBLE PRECISION DEFAULT NULL, trans_fat DOUBLE PRECISION DEFAULT NULL, dietary_fiber DOUBLE PRECISION DEFAULT NULL, sugar DOUBLE PRECISION DEFAULT NULL, protein DOUBLE PRECISION DEFAULT NULL, cholesterol DOUBLE PRECISION DEFAULT NULL, sodium DOUBLE PRECISION DEFAULT NULL, potassium DOUBLE PRECISION DEFAULT NULL, vit_a DOUBLE PRECISION DEFAULT NULL, vit_c DOUBLE PRECISION DEFAULT NULL, calcium DOUBLE PRECISION DEFAULT NULL, iron DOUBLE PRECISION DEFAULT NULL, carbohydrate DOUBLE PRECISION DEFAULT NULL, date_time DATETIME NOT NULL, UNIQUE INDEX UNIQ_2F390D6A2B6FCFB2 (guid), INDEX IDX_2F390D6A639666D6 (meal_id), INDEX IDX_2F390D6A1984B75B (tracking_device_id), INDEX IDX_2F390D6A6B899279 (patient_id), UNIQUE INDEX DeviceRemote (remote_id, meal_id, patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE part_of_day (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_EE0DB1222B6FCFB2 (guid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', uuid VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, sur_name VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, ui_settings JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', rpg_factor DOUBLE PRECISION DEFAULT NULL, first_run TINYINT(1) DEFAULT NULL, email VARCHAR(255) NOT NULL, date_of_birth DATETIME DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, last_logged_in DATETIME DEFAULT NULL, login_streak INT DEFAULT NULL, UNIQUE INDEX UNIQ_1ADAD7EB2B6FCFB2 (guid), UNIQUE INDEX UNIQ_1ADAD7EBD17F50A6 (uuid), UNIQUE INDEX UNIQ_1ADAD7EB7BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_credentials (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, service_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', token LONGTEXT NOT NULL, refresh_token VARCHAR(255) DEFAULT NULL, expires DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_CA0617412B6FCFB2 (guid), INDEX IDX_CA0617416B899279 (patient_id), INDEX IDX_CA061741ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_device (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_agent LONGTEXT DEFAULT NULL, os VARCHAR(255) DEFAULT NULL, browser VARCHAR(255) DEFAULT NULL, device VARCHAR(255) DEFAULT NULL, os_version VARCHAR(255) DEFAULT NULL, browser_version VARCHAR(255) DEFAULT NULL, sms LONGTEXT DEFAULT NULL, last_seen DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, app VARCHAR(255) DEFAULT NULL, version VARCHAR(255) DEFAULT NULL, production TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_2615F742B6FCFB2 (guid), INDEX IDX_2615F746B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_friends (id INT AUTO_INCREMENT NOT NULL, friend_a_id INT NOT NULL, friend_b_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', accepted TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_7C8D25822B6FCFB2 (guid), INDEX IDX_7C8D2582A1A48FB8 (friend_a_id), INDEX IDX_7C8D2582B3112056 (friend_b_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_goals (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, unit_of_measurement_id INT DEFAULT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', entity VARCHAR(255) NOT NULL, goal DOUBLE PRECISION NOT NULL, date_set DATETIME NOT NULL, UNIQUE INDEX UNIQ_5212C5E02B6FCFB2 (guid), INDEX IDX_5212C5E06B899279 (patient_id), INDEX IDX_5212C5E0DA7C9F35 (unit_of_measurement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_membership (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', tear VARCHAR(255) NOT NULL, since DATETIME NOT NULL, active TINYINT(1) NOT NULL, lifetime TINYINT(1) NOT NULL, last_paid DATETIME NOT NULL, UNIQUE INDEX UNIQ_303F746A2B6FCFB2 (guid), UNIQUE INDEX UNIQ_303F746A6B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_settings (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, service_id INT DEFAULT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, value JSON NOT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX UNIQ_D2C0F8602B6FCFB2 (guid), INDEX IDX_D2C0F8606B899279 (patient_id), INDEX IDX_D2C0F860ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_challenge_friends (id INT AUTO_INCREMENT NOT NULL, challenger_id INT NOT NULL, challenged_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', target INT NOT NULL, invite_date DATETIME NOT NULL, start_date DATETIME DEFAULT NULL, duration INT NOT NULL, criteria VARCHAR(255) NOT NULL, outcome INT DEFAULT NULL, end_date DATETIME DEFAULT NULL, challenger_sum INT DEFAULT NULL, challenged_sum INT DEFAULT NULL, challenger_details LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', challenged_details LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', completed_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5BDB23D2B6FCFB2 (guid), INDEX IDX_5BDB23D2D521FDF (challenger_id), INDEX IDX_5BDB23D5820179C (challenged_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_challenge_global (id INT AUTO_INCREMENT NOT NULL, child_of_id INT DEFAULT NULL, reward_id INT DEFAULT NULL, unit_of_measurement_id INT DEFAULT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, descripton LONGTEXT DEFAULT NULL, active TINYINT(1) DEFAULT NULL, criteria VARCHAR(255) DEFAULT NULL, target DOUBLE PRECISION DEFAULT NULL, progression VARCHAR(5) DEFAULT NULL, xp DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_F6479702B6FCFB2 (guid), INDEX IDX_F64797090B01EF (child_of_id), UNIQUE INDEX UNIQ_F647970E466ACA1 (reward_id), INDEX IDX_F647970DA7C9F35 (unit_of_measurement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_challenge_global_patient (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, challenge_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', criteria VARCHAR(255) NOT NULL, start_date_time DATETIME NOT NULL, finish_date_time DATETIME DEFAULT NULL, progress DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_AA07E8632B6FCFB2 (guid), INDEX IDX_AA07E8636B899279 (patient_id), INDEX IDX_AA07E86398A21AC6 (challenge_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_indicator (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, data_set VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, comparator VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F75BD9DA2B6FCFB2 (guid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_milestones (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', category VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, msg_less VARCHAR(255) DEFAULT NULL, msg_more VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_2CBF373C2B6FCFB2 (guid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_rewards (id INT AUTO_INCREMENT NOT NULL, indicator_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, text VARCHAR(255) NOT NULL, text_long LONGTEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, payload LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_FBCA13082B6FCFB2 (guid), INDEX IDX_FBCA13084402854A (indicator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_rewards_awarded (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, reward_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', datetime DATETIME NOT NULL, UNIQUE INDEX UNIQ_A069EAE62B6FCFB2 (guid), INDEX IDX_A069EAE66B899279 (patient_id), INDEX IDX_A069EAE6E466ACA1 (reward_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rpg_xp (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', datetime DATETIME NOT NULL, reason VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_7808C9532B6FCFB2 (guid), INDEX IDX_7808C9536B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site_nav_item (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, divider TINYINT(1) DEFAULT \'0\' NOT NULL, title TINYINT(1) DEFAULT \'0\' NOT NULL, icon VARCHAR(255) DEFAULT NULL, display_order INT DEFAULT 0 NOT NULL, badge_variant VARCHAR(255) DEFAULT NULL, badge_text VARCHAR(255) DEFAULT NULL, child_of INT DEFAULT 0 NOT NULL, access_level VARCHAR(255) DEFAULT NULL, in_development TINYINT(1) DEFAULT \'0\' NOT NULL, require_service LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_D8A8D9AF2B6FCFB2 (guid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site_news (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, published DATETIME NOT NULL, expires DATETIME DEFAULT NULL, accent VARCHAR(255) DEFAULT NULL, displayed TINYINT(1) DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, priority INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_264924A92B6FCFB2 (guid), INDEX IDX_264924A96B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sync_queue (id INT AUTO_INCREMENT NOT NULL, credentials_id INT NOT NULL, service_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', endpoint VARCHAR(255) NOT NULL, datetime DATETIME NOT NULL, UNIQUE INDEX UNIQ_99CCF5CE2B6FCFB2 (guid), INDEX IDX_99CCF5CE41E8B2E5 (credentials_id), INDEX IDX_99CCF5CEED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE third_party_service (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_B4645ADE2B6FCFB2 (guid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tracking_device (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, service_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(150) DEFAULT NULL, comment VARCHAR(200) DEFAULT NULL, battery INT DEFAULT NULL, last_synced DATETIME DEFAULT NULL, remote_id VARCHAR(255) DEFAULT NULL, type VARCHAR(150) DEFAULT NULL, manufacturer VARCHAR(255) DEFAULT NULL, model VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_A1A335C22B6FCFB2 (guid), INDEX IDX_A1A335C26B899279 (patient_id), INDEX IDX_A1A335C2ED5CA9E6 (service_id), UNIQUE INDEX DeviceService (remote_id, service_id, patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit_of_measurement (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_DC68A6042B6FCFB2 (guid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE uploaded_file (id INT AUTO_INCREMENT NOT NULL, license_id INT NOT NULL, exercise_id INT DEFAULT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT \'url\' NOT NULL, UNIQUE INDEX UNIQ_B40DF75D2B6FCFB2 (guid), UNIQUE INDEX UNIQ_B40DF75DB548B0F (path), INDEX IDX_B40DF75D460F904B (license_id), INDEX IDX_B40DF75DE934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workout_categories (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_56D709E22B6FCFB2 (guid), UNIQUE INDEX UNIQ_56D709E25E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workout_equipment (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F1E2B2D72B6FCFB2 (guid), UNIQUE INDEX UNIQ_F1E2B2D75E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workout_exercise (id INT AUTO_INCREMENT NOT NULL, equipment_id INT DEFAULT NULL, license_id INT DEFAULT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_76AB38AA2B6FCFB2 (guid), INDEX IDX_76AB38AA517FE9FE (equipment_id), INDEX IDX_76AB38AA460F904B (license_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workout_exercise_workout_categories (workout_exercise_id INT NOT NULL, workout_categories_id INT NOT NULL, INDEX IDX_FE7786CDE435DB6B (workout_exercise_id), INDEX IDX_FE7786CD5523D96 (workout_categories_id), PRIMARY KEY(workout_exercise_id, workout_categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workout_muscle (id INT AUTO_INCREMENT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, is_front TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_E99206212B6FCFB2 (guid), UNIQUE INDEX UNIQ_E99206215E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workout_muscle_relation (id INT AUTO_INCREMENT NOT NULL, muscle_id INT NOT NULL, exercise_id INT NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', is_primary TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_56B0B0112B6FCFB2 (guid), INDEX IDX_56B0B011354FDBB4 (muscle_id), INDEX IDX_56B0B011E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
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
        $this->addSql('ALTER TABLE patient_device ADD CONSTRAINT FK_2615F746B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE patient_friends ADD CONSTRAINT FK_7C8D2582A1A48FB8 FOREIGN KEY (friend_a_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE patient_friends ADD CONSTRAINT FK_7C8D2582B3112056 FOREIGN KEY (friend_b_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE patient_goals ADD CONSTRAINT FK_5212C5E06B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE patient_goals ADD CONSTRAINT FK_5212C5E0DA7C9F35 FOREIGN KEY (unit_of_measurement_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE patient_membership ADD CONSTRAINT FK_303F746A6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE patient_settings ADD CONSTRAINT FK_D2C0F8606B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE patient_settings ADD CONSTRAINT FK_D2C0F860ED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE rpg_challenge_friends ADD CONSTRAINT FK_5BDB23D2D521FDF FOREIGN KEY (challenger_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE rpg_challenge_friends ADD CONSTRAINT FK_5BDB23D5820179C FOREIGN KEY (challenged_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE rpg_challenge_global ADD CONSTRAINT FK_F64797090B01EF FOREIGN KEY (child_of_id) REFERENCES rpg_challenge_global (id)');
        $this->addSql('ALTER TABLE rpg_challenge_global ADD CONSTRAINT FK_F647970E466ACA1 FOREIGN KEY (reward_id) REFERENCES rpg_rewards (id)');
        $this->addSql('ALTER TABLE rpg_challenge_global ADD CONSTRAINT FK_F647970DA7C9F35 FOREIGN KEY (unit_of_measurement_id) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE rpg_challenge_global_patient ADD CONSTRAINT FK_AA07E8636B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE rpg_challenge_global_patient ADD CONSTRAINT FK_AA07E86398A21AC6 FOREIGN KEY (challenge_id) REFERENCES rpg_challenge_global (id)');
        $this->addSql('ALTER TABLE rpg_rewards ADD CONSTRAINT FK_FBCA13084402854A FOREIGN KEY (indicator_id) REFERENCES rpg_indicator (id)');
        $this->addSql('ALTER TABLE rpg_rewards_awarded ADD CONSTRAINT FK_A069EAE66B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE rpg_rewards_awarded ADD CONSTRAINT FK_A069EAE6E466ACA1 FOREIGN KEY (reward_id) REFERENCES rpg_rewards (id)');
        $this->addSql('ALTER TABLE rpg_xp ADD CONSTRAINT FK_7808C9536B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE site_news ADD CONSTRAINT FK_264924A96B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE sync_queue ADD CONSTRAINT FK_99CCF5CE41E8B2E5 FOREIGN KEY (credentials_id) REFERENCES patient_credentials (id)');
        $this->addSql('ALTER TABLE sync_queue ADD CONSTRAINT FK_99CCF5CEED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE tracking_device ADD CONSTRAINT FK_A1A335C26B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE tracking_device ADD CONSTRAINT FK_A1A335C2ED5CA9E6 FOREIGN KEY (service_id) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE uploaded_file ADD CONSTRAINT FK_B40DF75D460F904B FOREIGN KEY (license_id) REFERENCES contribution_license (id)');
        $this->addSql('ALTER TABLE uploaded_file ADD CONSTRAINT FK_B40DF75DE934951A FOREIGN KEY (exercise_id) REFERENCES workout_exercise (id)');
        $this->addSql('ALTER TABLE workout_exercise ADD CONSTRAINT FK_76AB38AA517FE9FE FOREIGN KEY (equipment_id) REFERENCES workout_equipment (id)');
        $this->addSql('ALTER TABLE workout_exercise ADD CONSTRAINT FK_76AB38AA460F904B FOREIGN KEY (license_id) REFERENCES contribution_license (id)');
        $this->addSql('ALTER TABLE workout_exercise_workout_categories ADD CONSTRAINT FK_FE7786CDE435DB6B FOREIGN KEY (workout_exercise_id) REFERENCES workout_exercise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workout_exercise_workout_categories ADD CONSTRAINT FK_FE7786CD5523D96 FOREIGN KEY (workout_categories_id) REFERENCES workout_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workout_muscle_relation ADD CONSTRAINT FK_56B0B011354FDBB4 FOREIGN KEY (muscle_id) REFERENCES workout_muscle (id)');
        $this->addSql('ALTER TABLE workout_muscle_relation ADD CONSTRAINT FK_56B0B011E934951A FOREIGN KEY (exercise_id) REFERENCES workout_exercise (id)');
    }
}
