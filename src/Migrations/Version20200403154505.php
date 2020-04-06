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
final class Version20200403154505 extends AbstractMigration
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

        $this->addSql('ALTER TABLE api_access_log CHANGE cooldown cooldown DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE body_composition CHANGE skeletal_muscle skeletal_muscle DOUBLE PRECISION DEFAULT NULL, CHANGE muscle_mass muscle_mass DOUBLE PRECISION DEFAULT NULL, CHANGE basal_metabolic_rate basal_metabolic_rate DOUBLE PRECISION DEFAULT NULL, CHANGE skeletal_muscle_mass skeletal_muscle_mass DOUBLE PRECISION DEFAULT NULL, CHANGE total_body_water total_body_water DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE body_fat CHANGE part_of_day_id part_of_day_id INT DEFAULT NULL, CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL, CHANGE fat_free_mass fat_free_mass DOUBLE PRECISION DEFAULT NULL, CHANGE fat_free fat_free DOUBLE PRECISION DEFAULT NULL, CHANGE body_fat_mass body_fat_mass DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE body_weight CHANGE part_of_day_id part_of_day_id INT DEFAULT NULL, CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE consume_caffeine CHANGE patient_goal_id patient_goal_id INT DEFAULT NULL, CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL, CHANGE comment comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE consume_water CHANGE patient_goal_id patient_goal_id INT DEFAULT NULL, CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL, CHANGE comment comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE contribution_license CHANGE link link VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE exercise CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL, CHANGE steps steps INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exercise_summary CHANGE altitude_gain altitude_gain DOUBLE PRECISION DEFAULT NULL, CHANGE altitude_loss altitude_loss DOUBLE PRECISION DEFAULT NULL, CHANGE altitude_max altitude_max DOUBLE PRECISION DEFAULT NULL, CHANGE altitude_min altitude_min DOUBLE PRECISION DEFAULT NULL, CHANGE cadence_max cadence_max DOUBLE PRECISION DEFAULT NULL, CHANGE cadence_mean cadence_mean DOUBLE PRECISION DEFAULT NULL, CHANGE cadence_min cadence_min DOUBLE PRECISION DEFAULT NULL, CHANGE calorie calorie DOUBLE PRECISION DEFAULT NULL, CHANGE distance_incline distance_incline DOUBLE PRECISION DEFAULT NULL, CHANGE distance_decline distance_decline DOUBLE PRECISION DEFAULT NULL, CHANGE distance distance DOUBLE PRECISION DEFAULT NULL, CHANGE speed_max speed_max DOUBLE PRECISION DEFAULT NULL, CHANGE speed_mean speed_mean DOUBLE PRECISION DEFAULT NULL, CHANGE heart_rate_max heart_rate_max DOUBLE PRECISION DEFAULT NULL, CHANGE heart_rate_mean heart_rate_mean DOUBLE PRECISION DEFAULT NULL, CHANGE heart_rate_min heart_rate_min DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE exercise_type CHANGE tag tag VARCHAR(255) DEFAULT NULL, CHANGE met met DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE fit_calories_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fit_distance_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fit_floors_intra_day CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fit_steps_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fit_steps_intra_day CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL, CHANGE duration duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE food_database CHANGE serving_unit_id serving_unit_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE calorie calorie DOUBLE PRECISION DEFAULT NULL, CHANGE serving_amount serving_amount DOUBLE PRECISION DEFAULT NULL, CHANGE total_fat total_fat DOUBLE PRECISION DEFAULT NULL, CHANGE saturated_fat saturated_fat DOUBLE PRECISION DEFAULT NULL, CHANGE carbohydrate carbohydrate DOUBLE PRECISION DEFAULT NULL, CHANGE dietary_fiber dietary_fiber DOUBLE PRECISION DEFAULT NULL, CHANGE sugar sugar DOUBLE PRECISION DEFAULT NULL, CHANGE protein protein DOUBLE PRECISION DEFAULT NULL, CHANGE serving_description serving_description VARCHAR(255) DEFAULT NULL, CHANGE serving_number_default serving_number_default DOUBLE PRECISION DEFAULT NULL, CHANGE remote_ids remote_ids JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE food_diary CHANGE comment comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE food_nutrition CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL, CHANGE calorie calorie DOUBLE PRECISION DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE total_fat total_fat DOUBLE PRECISION DEFAULT NULL, CHANGE saturated_fat saturated_fat DOUBLE PRECISION DEFAULT NULL, CHANGE polysaturated_fat polysaturated_fat DOUBLE PRECISION DEFAULT NULL, CHANGE monosaturated_fat monosaturated_fat DOUBLE PRECISION DEFAULT NULL, CHANGE trans_fat trans_fat DOUBLE PRECISION DEFAULT NULL, CHANGE dietary_fiber dietary_fiber DOUBLE PRECISION DEFAULT NULL, CHANGE sugar sugar DOUBLE PRECISION DEFAULT NULL, CHANGE protein protein DOUBLE PRECISION DEFAULT NULL, CHANGE cholesterol cholesterol DOUBLE PRECISION DEFAULT NULL, CHANGE sodium sodium DOUBLE PRECISION DEFAULT NULL, CHANGE potassium potassium DOUBLE PRECISION DEFAULT NULL, CHANGE vit_a vit_a DOUBLE PRECISION DEFAULT NULL, CHANGE vit_c vit_c DOUBLE PRECISION DEFAULT NULL, CHANGE calcium calcium DOUBLE PRECISION DEFAULT NULL, CHANGE iron iron DOUBLE PRECISION DEFAULT NULL, CHANGE carbohydrate carbohydrate DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE patient CHANGE roles roles JSON NOT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE sur_name sur_name VARCHAR(255) DEFAULT NULL, CHANGE avatar avatar VARCHAR(255) DEFAULT NULL, CHANGE ui_settings ui_settings JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE rpg_factor rpg_factor DOUBLE PRECISION DEFAULT NULL, CHANGE first_run first_run TINYINT(1) DEFAULT NULL, CHANGE date_of_birth date_of_birth DATETIME DEFAULT NULL, CHANGE gender gender VARCHAR(255) DEFAULT NULL, CHANGE last_logged_in last_logged_in DATETIME DEFAULT NULL, CHANGE login_streak login_streak INT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_credentials CHANGE refresh_token refresh_token VARCHAR(255) DEFAULT NULL, CHANGE expires expires DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_device CHANGE os os VARCHAR(255) DEFAULT NULL, CHANGE browser browser VARCHAR(255) DEFAULT NULL, CHANGE device device VARCHAR(255) DEFAULT NULL, CHANGE os_version os_version VARCHAR(255) DEFAULT NULL, CHANGE browser_version browser_version VARCHAR(255) DEFAULT NULL, CHANGE last_seen last_seen DATETIME DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE app app VARCHAR(255) DEFAULT NULL, CHANGE version version VARCHAR(255) DEFAULT NULL, CHANGE production production TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_friends CHANGE accepted accepted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_goals CHANGE unit_of_measurement_id unit_of_measurement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_membership CHANGE patient_id patient_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_settings CHANGE service_id service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rpg_challenge_friends CHANGE start_date start_date DATETIME DEFAULT NULL, CHANGE outcome outcome INT DEFAULT NULL, CHANGE end_date end_date DATETIME DEFAULT NULL, CHANGE challenger_sum challenger_sum INT DEFAULT NULL, CHANGE challenged_sum challenged_sum INT DEFAULT NULL, CHANGE challenger_details challenger_details LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE challenged_details challenged_details LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE completed_at completed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE rpg_challenge_global CHANGE child_of_id child_of_id INT DEFAULT NULL, CHANGE reward_id reward_id INT DEFAULT NULL, CHANGE unit_of_measurement_id unit_of_measurement_id INT DEFAULT NULL, CHANGE active active TINYINT(1) DEFAULT NULL, CHANGE criteria criteria VARCHAR(255) DEFAULT NULL, CHANGE target target DOUBLE PRECISION DEFAULT NULL, CHANGE progression progression VARCHAR(5) DEFAULT NULL, CHANGE xp xp DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE rpg_challenge_global_patient CHANGE finish_date_time finish_date_time DATETIME DEFAULT NULL, CHANGE progress progress DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE rpg_milestones CHANGE msg_less msg_less VARCHAR(255) DEFAULT NULL, CHANGE msg_more msg_more VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE rpg_rewards CHANGE xp xp DOUBLE PRECISION DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE site_nav_item CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE url url VARCHAR(255) DEFAULT NULL, CHANGE icon icon VARCHAR(255) DEFAULT NULL, CHANGE badge_variant badge_variant VARCHAR(255) DEFAULT NULL, CHANGE badge_text badge_text VARCHAR(255) DEFAULT NULL, CHANGE access_level access_level VARCHAR(255) DEFAULT NULL, CHANGE require_service require_service LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE site_news CHANGE patient_id patient_id INT DEFAULT NULL, CHANGE expires expires DATETIME DEFAULT NULL, CHANGE accent accent VARCHAR(255) DEFAULT NULL, CHANGE displayed displayed TINYINT(1) DEFAULT NULL, CHANGE link link VARCHAR(255) DEFAULT NULL, CHANGE priority priority INT DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE third_party_service CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tracking_device CHANGE name name VARCHAR(150) DEFAULT NULL, CHANGE comment comment VARCHAR(200) DEFAULT NULL, CHANGE battery battery INT DEFAULT NULL, CHANGE last_synced last_synced DATETIME DEFAULT NULL, CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(150) DEFAULT NULL, CHANGE manufacturer manufacturer VARCHAR(255) DEFAULT NULL, CHANGE model model VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE uploaded_file CHANGE exercise_id exercise_id INT DEFAULT NULL, CHANGE type type VARCHAR(255) DEFAULT \'url\' NOT NULL');
        $this->addSql('ALTER TABLE workout_exercise CHANGE equipment_id equipment_id INT DEFAULT NULL, CHANGE license_id license_id INT DEFAULT NULL');
        $this->addSql('INSERT INTO site_nav_item VALUES(1, \'Dashboard\', \'/dashboard\', 0, 0, \'fa fa-dashboard\', -1000, NULL, NULL, 0, NULL, 0, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(2, \'Fun\', NULL, 0, 1, NULL, -250, NULL, NULL, 0, NULL, 0, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(4, \'Awards\', \'/achievements/awards\', 0, 0, \'fa fa-diamond\', -249, NULL, NULL, 0, NULL, 0, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(6, \'Leaderboard\', \'/rpg/leaderboard\', 0, 0, \'fa fa-users\', -239, NULL, NULL, 0, NULL, 0, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(7, \'1:1 Challenges\', \'/rpg/challenges\', 0, 0, \'fa fa-trophy\', -219, NULL, NULL, 0, NULL, 0, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(8, \'Stats\', NULL, 0, 1, NULL, -450, NULL, NULL, 0, NULL, 0, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(11, \'Administration\', NULL, 0, 1, NULL, 900, NULL, NULL, 0, NULL, 0, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(12, \'Your Account\', \'/setup\', 0, 0, \'fa fa-cogs\', 980, NULL, NULL, 0, NULL, 0, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(13, \'Body Weight\', \'/body/weight\', 0, 0, \'medicalIcons-scale-tool-to-control-body-weight-standing-on-it\', -429, NULL, NULL, 0, NULL, 0, \'a:1:{i:0;s:21:\"App\\Entity\\BodyWeight\";}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(14, \'Activity Log\', \'/activities/log\', 0, 0, \'fa fa-archive\', -419, NULL, NULL, 0, NULL, 0, \'a:1:{i:0;s:19:\"App\\Entity\\Exercise\";}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(15, \'Profile\', \'/setup/profile\', 0, 0, \'fa fa-user\', 981, NULL, NULL, 12, NULL, 0, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(16, \'Select Your Account\', \'/setup/oauth\', 0, 0, \'fa fa-external-link\', 982, NULL, NULL, 12, NULL, 0, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(17, \'Activity Tracker\', \'/activities/tracker\', 0, 0, \'fa fa-percent\', -418, NULL, NULL, 0, \'disabled\', 1, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(18, \'Help\', NULL, 1, 1, NULL, 1000, NULL, NULL, 0, NULL, 1, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(19, \'Patreon\', \'/help/patreon\', 0, 0, \'fa fa-dollar\', 1010, NULL, NULL, 22, NULL, 1, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(20, \'Privacy Policy\', \'/help/privacy\', 0, 0, \'fa fa-low-vision\', 1020, NULL, NULL, 22, NULL, 1, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(21, \'Terms of Service\', \'/help/terms\', 0, 0, \'fa fa-legal\', 1030, NULL, NULL, 22, NULL, 1, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(22, \'Help\', \'/help\', 0, 0, \'fa fa-life-saver\', 1001, NULL, NULL, 0, NULL, 1, \'a:0:{}\')');
        $this->addSql('INSERT INTO site_nav_item VALUES(23, \'Challenges\', \'/rpg/pve/challenges\', 0, 0, \'adventureIcons-sports-10\', -229, NULL, NULL, 0, NULL, 0, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(24, \'Workouts\', NULL, 0, 1, NULL, -760, NULL, NULL, 0, NULL, 1, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(26, \'Exercises\', \'/exercises/overview\', 0, 0, \'fitnessIcons-person\', -749, NULL, NULL, 37, NULL, 1, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(27, \'New Exercise\', \'/exercises/add\', 0, 0, \'fitnessIcons-person\', 951, NULL, NULL, 30, \'ROLE_CONTRIBUTOR\', 1, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(30, \'Exercises\', \'/exercises/admin\', 0, 0, \'ico-cogs\', 950, NULL, NULL, 0, \'ROLE_CONTRIBUTOR\', 1, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(31, \'Muscle Overview\', \'/exercises/muscle/overview\', 0, 0, \'fitnessIcons-silhouette-2\', -748, NULL, NULL, 37, NULL, 1, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(32, \'New Muscle\', \'/exercises/muscle/add\', 0, 0, \'fitnessIcons-silhouette-2\', 952, NULL, NULL, 30, \'ROLE_CONTRIBUTOR\', 1, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(33, \'Equipment Overview\', \'/exercises/equipment/overview\', 0, 0, \'fitnessIcons-silhouette-4\', -747, NULL, NULL, 37, NULL, 1, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(34, \'New Equipment\', \'/exercises/equipment/add\', 0, 0, \'fitnessIcons-silhouette-4\', 953, NULL, NULL, 30, \'ROLE_CONTRIBUTOR\', 1, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(35, \'Category Overview\', \'/exercises/category/overview\', 0, 0, \'fitnessIcons-people-1\', -746, NULL, NULL, 37, NULL, 1, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(36, \'New Category\', \'/exercises/category/add\', 0, 0, \'fitnessIcons-people-1\', 954, NULL, NULL, 30, \'ROLE_CONTRIBUTOR\', 1, NULL)');
        $this->addSql('INSERT INTO site_nav_item VALUES(37, \'Exercise\', \'/exercises\', 0, 0, \'fitnessIcons-silhouette-1\', -750, NULL, NULL, 0, NULL, 1, NULL)');

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

        $this->addSql('ALTER TABLE api_access_log CHANGE cooldown cooldown DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE body_composition CHANGE skeletal_muscle skeletal_muscle DOUBLE PRECISION DEFAULT \'NULL\', CHANGE muscle_mass muscle_mass DOUBLE PRECISION DEFAULT \'NULL\', CHANGE basal_metabolic_rate basal_metabolic_rate DOUBLE PRECISION DEFAULT \'NULL\', CHANGE skeletal_muscle_mass skeletal_muscle_mass DOUBLE PRECISION DEFAULT \'NULL\', CHANGE total_body_water total_body_water DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE body_fat CHANGE part_of_day_id part_of_day_id INT DEFAULT NULL, CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE fat_free_mass fat_free_mass DOUBLE PRECISION DEFAULT \'NULL\', CHANGE fat_free fat_free DOUBLE PRECISION DEFAULT \'NULL\', CHANGE body_fat_mass body_fat_mass DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE body_weight CHANGE part_of_day_id part_of_day_id INT DEFAULT NULL, CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE consume_caffeine CHANGE patient_goal_id patient_goal_id INT DEFAULT NULL, CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE comment comment VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE consume_water CHANGE patient_goal_id patient_goal_id INT DEFAULT NULL, CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE comment comment VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE contribution_license CHANGE link link VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE exercise CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE steps steps INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exercise_summary CHANGE altitude_gain altitude_gain DOUBLE PRECISION DEFAULT \'NULL\', CHANGE altitude_loss altitude_loss DOUBLE PRECISION DEFAULT \'NULL\', CHANGE altitude_max altitude_max DOUBLE PRECISION DEFAULT \'NULL\', CHANGE altitude_min altitude_min DOUBLE PRECISION DEFAULT \'NULL\', CHANGE cadence_max cadence_max DOUBLE PRECISION DEFAULT \'NULL\', CHANGE cadence_mean cadence_mean DOUBLE PRECISION DEFAULT \'NULL\', CHANGE cadence_min cadence_min DOUBLE PRECISION DEFAULT \'NULL\', CHANGE calorie calorie DOUBLE PRECISION DEFAULT \'NULL\', CHANGE distance_incline distance_incline DOUBLE PRECISION DEFAULT \'NULL\', CHANGE distance_decline distance_decline DOUBLE PRECISION DEFAULT \'NULL\', CHANGE distance distance DOUBLE PRECISION DEFAULT \'NULL\', CHANGE speed_max speed_max DOUBLE PRECISION DEFAULT \'NULL\', CHANGE speed_mean speed_mean DOUBLE PRECISION DEFAULT \'NULL\', CHANGE heart_rate_max heart_rate_max DOUBLE PRECISION DEFAULT \'NULL\', CHANGE heart_rate_mean heart_rate_mean DOUBLE PRECISION DEFAULT \'NULL\', CHANGE heart_rate_min heart_rate_min DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE exercise_type CHANGE tag tag VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE met met DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE fit_calories_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fit_distance_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fit_floors_intra_day CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fit_steps_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fit_steps_intra_day CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE duration duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE food_database CHANGE serving_unit_id serving_unit_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE calorie calorie DOUBLE PRECISION DEFAULT \'NULL\', CHANGE serving_amount serving_amount DOUBLE PRECISION DEFAULT \'NULL\', CHANGE total_fat total_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE saturated_fat saturated_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE carbohydrate carbohydrate DOUBLE PRECISION DEFAULT \'NULL\', CHANGE dietary_fiber dietary_fiber DOUBLE PRECISION DEFAULT \'NULL\', CHANGE sugar sugar DOUBLE PRECISION DEFAULT \'NULL\', CHANGE protein protein DOUBLE PRECISION DEFAULT \'NULL\', CHANGE serving_description serving_description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE serving_number_default serving_number_default DOUBLE PRECISION DEFAULT \'NULL\', CHANGE remote_ids remote_ids JSON DEFAULT \'NULL\' COLLATE utf8mb4_bin COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE food_diary CHANGE comment comment VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE food_nutrition CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE calorie calorie DOUBLE PRECISION DEFAULT \'NULL\', CHANGE title title VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE total_fat total_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE saturated_fat saturated_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE polysaturated_fat polysaturated_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE monosaturated_fat monosaturated_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE trans_fat trans_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE dietary_fiber dietary_fiber DOUBLE PRECISION DEFAULT \'NULL\', CHANGE sugar sugar DOUBLE PRECISION DEFAULT \'NULL\', CHANGE protein protein DOUBLE PRECISION DEFAULT \'NULL\', CHANGE cholesterol cholesterol DOUBLE PRECISION DEFAULT \'NULL\', CHANGE sodium sodium DOUBLE PRECISION DEFAULT \'NULL\', CHANGE potassium potassium DOUBLE PRECISION DEFAULT \'NULL\', CHANGE vit_a vit_a DOUBLE PRECISION DEFAULT \'NULL\', CHANGE vit_c vit_c DOUBLE PRECISION DEFAULT \'NULL\', CHANGE calcium calcium DOUBLE PRECISION DEFAULT \'NULL\', CHANGE iron iron DOUBLE PRECISION DEFAULT \'NULL\', CHANGE carbohydrate carbohydrate DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE patient CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE first_name first_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE sur_name sur_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE avatar avatar VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE ui_settings ui_settings JSON DEFAULT \'NULL\' COLLATE utf8mb4_bin COMMENT \'(DC2Type:json_array)\', CHANGE rpg_factor rpg_factor DOUBLE PRECISION DEFAULT \'NULL\', CHANGE first_run first_run TINYINT(1) DEFAULT \'NULL\', CHANGE date_of_birth date_of_birth DATETIME DEFAULT \'NULL\', CHANGE gender gender VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE last_logged_in last_logged_in DATETIME DEFAULT \'NULL\', CHANGE login_streak login_streak INT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_credentials CHANGE refresh_token refresh_token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE expires expires DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE patient_device CHANGE os os VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE browser browser VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE device device VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE os_version os_version VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE browser_version browser_version VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE last_seen last_seen DATETIME DEFAULT \'NULL\', CHANGE name name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE app app VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE version version VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE production production TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE patient_friends CHANGE accepted accepted TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE patient_goals CHANGE unit_of_measurement_id unit_of_measurement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_membership CHANGE patient_id patient_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_settings CHANGE service_id service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rpg_challenge_friends CHANGE start_date start_date DATETIME DEFAULT \'NULL\', CHANGE outcome outcome INT DEFAULT NULL, CHANGE end_date end_date DATETIME DEFAULT \'NULL\', CHANGE challenger_sum challenger_sum INT DEFAULT NULL, CHANGE challenged_sum challenged_sum INT DEFAULT NULL, CHANGE challenger_details challenger_details LONGTEXT DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', CHANGE challenged_details challenged_details LONGTEXT DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', CHANGE completed_at completed_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE rpg_challenge_global CHANGE child_of_id child_of_id INT DEFAULT NULL, CHANGE reward_id reward_id INT DEFAULT NULL, CHANGE unit_of_measurement_id unit_of_measurement_id INT DEFAULT NULL, CHANGE active active TINYINT(1) DEFAULT \'NULL\', CHANGE criteria criteria VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE target target DOUBLE PRECISION DEFAULT \'NULL\', CHANGE progression progression VARCHAR(5) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE xp xp DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE rpg_challenge_global_patient CHANGE finish_date_time finish_date_time DATETIME DEFAULT \'NULL\', CHANGE progress progress DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE rpg_milestones CHANGE msg_less msg_less VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE msg_more msg_more VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE rpg_rewards CHANGE xp xp DOUBLE PRECISION DEFAULT \'NULL\', CHANGE image image VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE site_nav_item CHANGE name name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE icon icon VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE badge_variant badge_variant VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE badge_text badge_text VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE access_level access_level VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE require_service require_service LONGTEXT DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE site_news CHANGE patient_id patient_id INT DEFAULT NULL, CHANGE expires expires DATETIME DEFAULT \'NULL\', CHANGE accent accent VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE displayed displayed TINYINT(1) DEFAULT \'NULL\', CHANGE link link VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE priority priority INT DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE third_party_service CHANGE name name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE tracking_device CHANGE name name VARCHAR(150) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE comment comment VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE battery battery INT DEFAULT NULL, CHANGE last_synced last_synced DATETIME DEFAULT \'NULL\', CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE type type VARCHAR(150) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE manufacturer manufacturer VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE model model VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE uploaded_file CHANGE exercise_id exercise_id INT DEFAULT NULL, CHANGE type type VARCHAR(255) DEFAULT \'url\' NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE workout_exercise CHANGE equipment_id equipment_id INT DEFAULT NULL, CHANGE license_id license_id INT DEFAULT NULL');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 1');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 2');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 4');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 6');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 7');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 8');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 11');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 12');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 13');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 14');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 15');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 16');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 17');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 18');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 19');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 20');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 21');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 22');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 23');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 24');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 26');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 27');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 30');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 31');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 32');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 33');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 34');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 35');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 36');
        $this->addSql('DELETE FROM site_nav_item WHERE id = 37');

    }
}
