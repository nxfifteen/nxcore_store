<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190903165206 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

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
        $this->addSql('ALTER TABLE exercise CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE exercise_summary CHANGE altitude_gain altitude_gain DOUBLE PRECISION DEFAULT NULL, CHANGE altitude_loss altitude_loss DOUBLE PRECISION DEFAULT NULL, CHANGE altitude_max altitude_max DOUBLE PRECISION DEFAULT NULL, CHANGE altitude_min altitude_min DOUBLE PRECISION DEFAULT NULL, CHANGE cadence_max cadence_max DOUBLE PRECISION DEFAULT NULL, CHANGE cadence_mean cadence_mean DOUBLE PRECISION DEFAULT NULL, CHANGE cadence_min cadence_min DOUBLE PRECISION DEFAULT NULL, CHANGE calorie calorie DOUBLE PRECISION DEFAULT NULL, CHANGE distance_incline distance_incline DOUBLE PRECISION DEFAULT NULL, CHANGE distance_decline distance_decline DOUBLE PRECISION DEFAULT NULL, CHANGE distance distance DOUBLE PRECISION DEFAULT NULL, CHANGE speed_max speed_max DOUBLE PRECISION DEFAULT NULL, CHANGE speed_mean speed_mean DOUBLE PRECISION DEFAULT NULL, CHANGE heart_rate_max heart_rate_max DOUBLE PRECISION DEFAULT NULL, CHANGE heart_rate_mean heart_rate_mean DOUBLE PRECISION DEFAULT NULL, CHANGE heart_rate_min heart_rate_min DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE exercise_track CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL, CHANGE altitude altitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE fit_calories_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fit_distance_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fit_floors_intra_day CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fit_steps_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fit_steps_intra_day CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL, CHANGE duration duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE food_database ADD remote_ids JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE serving_unit_id serving_unit_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE calorie calorie DOUBLE PRECISION DEFAULT NULL, CHANGE serving_amount serving_amount DOUBLE PRECISION DEFAULT NULL, CHANGE total_fat total_fat DOUBLE PRECISION DEFAULT NULL, CHANGE saturated_fat saturated_fat DOUBLE PRECISION DEFAULT NULL, CHANGE carbohydrate carbohydrate DOUBLE PRECISION DEFAULT NULL, CHANGE dietary_fiber dietary_fiber DOUBLE PRECISION DEFAULT NULL, CHANGE sugar sugar DOUBLE PRECISION DEFAULT NULL, CHANGE protein protein DOUBLE PRECISION DEFAULT NULL, CHANGE serving_description serving_description VARCHAR(255) DEFAULT NULL, CHANGE serving_number_default serving_number_default DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE food_nutrition CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL, CHANGE calorie calorie DOUBLE PRECISION DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE total_fat total_fat DOUBLE PRECISION DEFAULT NULL, CHANGE saturated_fat saturated_fat DOUBLE PRECISION DEFAULT NULL, CHANGE polysaturated_fat polysaturated_fat DOUBLE PRECISION DEFAULT NULL, CHANGE monosaturated_fat monosaturated_fat DOUBLE PRECISION DEFAULT NULL, CHANGE trans_fat trans_fat DOUBLE PRECISION DEFAULT NULL, CHANGE dietary_fiber dietary_fiber DOUBLE PRECISION DEFAULT NULL, CHANGE sugar sugar DOUBLE PRECISION DEFAULT NULL, CHANGE protein protein DOUBLE PRECISION DEFAULT NULL, CHANGE cholesterol cholesterol DOUBLE PRECISION DEFAULT NULL, CHANGE sodium sodium DOUBLE PRECISION DEFAULT NULL, CHANGE potassium potassium DOUBLE PRECISION DEFAULT NULL, CHANGE vit_a vit_a DOUBLE PRECISION DEFAULT NULL, CHANGE vit_c vit_c DOUBLE PRECISION DEFAULT NULL, CHANGE calcium calcium DOUBLE PRECISION DEFAULT NULL, CHANGE iron iron DOUBLE PRECISION DEFAULT NULL, CHANGE carbohydrate carbohydrate DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE patient CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE patient_goals CHANGE unit_of_measurement_id unit_of_measurement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE third_party_service CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tracking_device CHANGE name name VARCHAR(150) DEFAULT NULL, CHANGE comment comment VARCHAR(200) DEFAULT NULL, CHANGE battery battery INT DEFAULT NULL, CHANGE last_synced last_synced DATETIME DEFAULT NULL, CHANGE remote_id remote_id VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(150) DEFAULT NULL, CHANGE manufacturer manufacturer VARCHAR(255) DEFAULT NULL, CHANGE model model VARCHAR(255) DEFAULT NULL');
    }

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
        $this->addSql('ALTER TABLE exercise CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE exercise_summary CHANGE altitude_gain altitude_gain DOUBLE PRECISION DEFAULT \'NULL\', CHANGE altitude_loss altitude_loss DOUBLE PRECISION DEFAULT \'NULL\', CHANGE altitude_max altitude_max DOUBLE PRECISION DEFAULT \'NULL\', CHANGE altitude_min altitude_min DOUBLE PRECISION DEFAULT \'NULL\', CHANGE cadence_max cadence_max DOUBLE PRECISION DEFAULT \'NULL\', CHANGE cadence_mean cadence_mean DOUBLE PRECISION DEFAULT \'NULL\', CHANGE cadence_min cadence_min DOUBLE PRECISION DEFAULT \'NULL\', CHANGE calorie calorie DOUBLE PRECISION DEFAULT \'NULL\', CHANGE distance_incline distance_incline DOUBLE PRECISION DEFAULT \'NULL\', CHANGE distance_decline distance_decline DOUBLE PRECISION DEFAULT \'NULL\', CHANGE distance distance DOUBLE PRECISION DEFAULT \'NULL\', CHANGE speed_max speed_max DOUBLE PRECISION DEFAULT \'NULL\', CHANGE speed_mean speed_mean DOUBLE PRECISION DEFAULT \'NULL\', CHANGE heart_rate_max heart_rate_max DOUBLE PRECISION DEFAULT \'NULL\', CHANGE heart_rate_mean heart_rate_mean DOUBLE PRECISION DEFAULT \'NULL\', CHANGE heart_rate_min heart_rate_min DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE exercise_track CHANGE latitude latitude DOUBLE PRECISION DEFAULT \'NULL\', CHANGE longitude longitude DOUBLE PRECISION DEFAULT \'NULL\', CHANGE altitude altitude DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE fit_calories_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fit_distance_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fit_floors_intra_day CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fit_steps_daily_summary CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fit_steps_intra_day CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE duration duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE food_database DROP remote_ids, CHANGE serving_unit_id serving_unit_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE calorie calorie DOUBLE PRECISION DEFAULT \'NULL\', CHANGE serving_amount serving_amount DOUBLE PRECISION DEFAULT \'NULL\', CHANGE total_fat total_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE saturated_fat saturated_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE carbohydrate carbohydrate DOUBLE PRECISION DEFAULT \'NULL\', CHANGE dietary_fiber dietary_fiber DOUBLE PRECISION DEFAULT \'NULL\', CHANGE sugar sugar DOUBLE PRECISION DEFAULT \'NULL\', CHANGE protein protein DOUBLE PRECISION DEFAULT \'NULL\', CHANGE serving_description serving_description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE serving_number_default serving_number_default DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE food_nutrition CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE calorie calorie DOUBLE PRECISION DEFAULT \'NULL\', CHANGE title title VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE total_fat total_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE saturated_fat saturated_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE polysaturated_fat polysaturated_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE monosaturated_fat monosaturated_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE trans_fat trans_fat DOUBLE PRECISION DEFAULT \'NULL\', CHANGE dietary_fiber dietary_fiber DOUBLE PRECISION DEFAULT \'NULL\', CHANGE sugar sugar DOUBLE PRECISION DEFAULT \'NULL\', CHANGE protein protein DOUBLE PRECISION DEFAULT \'NULL\', CHANGE cholesterol cholesterol DOUBLE PRECISION DEFAULT \'NULL\', CHANGE sodium sodium DOUBLE PRECISION DEFAULT \'NULL\', CHANGE potassium potassium DOUBLE PRECISION DEFAULT \'NULL\', CHANGE vit_a vit_a DOUBLE PRECISION DEFAULT \'NULL\', CHANGE vit_c vit_c DOUBLE PRECISION DEFAULT \'NULL\', CHANGE calcium calcium DOUBLE PRECISION DEFAULT \'NULL\', CHANGE iron iron DOUBLE PRECISION DEFAULT \'NULL\', CHANGE carbohydrate carbohydrate DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE patient CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE patient_goals CHANGE unit_of_measurement_id unit_of_measurement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE third_party_service CHANGE name name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE tracking_device CHANGE name name VARCHAR(150) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE comment comment VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE battery battery INT DEFAULT NULL, CHANGE last_synced last_synced DATETIME DEFAULT \'NULL\', CHANGE remote_id remote_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE type type VARCHAR(150) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE manufacturer manufacturer VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE model model VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
