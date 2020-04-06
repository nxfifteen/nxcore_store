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
final class Version20200403215001 extends AbstractMigration
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
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(18, NULL, \'The Hobbit\', \'Walk, run, hike, bike, blade, swim - if you can measure the distance, you can do this challenge.\', 1, \'FitDistanceDailySummary\', 967, \'flow\', 15, 410, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(19, 18, \'Bag End to Rivendell\', \'\', 1, \'FitDistanceDailySummary\', 397, \'flow\', NULL, 10, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(20, 19, \'April 27 - Day 1: 11 miles\', \'Bilbo leaves Bag End and runs south down the lane toward Hobbiton.\', 1, \'FitDistanceDailySummary\', 0, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(21, 19, \'April 27 - Day 1: 11 miles\', \'Crosses bridge across the Water. Turns east on the road to Bywater.\', 1, \'FitDistanceDailySummary\', 0.5, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(22, 19, \'April 27 - Day 1: 11 miles\', \'Reaches The Green Dragon in Bywater by 11 a.m. [Note: an impossible feat, as he would have had to run 2 minute miles!]. The dwarves have a pony ready and they leave almost immediately. Gandalf soon joins them. Turn SE on Bywater Road. High banks with hedges rise on each side.\', 1, \'FitDistanceDailySummary\', 5, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(23, 19, \'April 27 - Day 1: 11 miles\', \'Reach the Great East Road. Turn east.\', 1, \'FitDistanceDailySummary\', 7, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(24, 19, \'April 27 - Day 1: 11 miles\', \'Pass Three-Farthing Stone.\', 1, \'FitDistanceDailySummary\', 9, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(25, 19, \'April 27 - Day 1: 11 miles\', \'Camp.\', 1, \'FitDistanceDailySummary\', 11, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(27, 19, \'April 28 - Day 2: 12 miles\', \'Frogmorton. Stay at The Floating Log.\', 1, \'FitDistanceDailySummary\', 23, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(29, 19, \'April 29 - Day 3: 11 miles\', \'See the trees of Woody End across the fields to the south. Camp.\', 1, \'FitDistanceDailySummary\', 34, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(31, 19, \'April 30 - Day 4: 11 miles\', \'Reach the Brandywine Bridge. Stay at the Bridge Inn.\', 1, \'FitDistanceDailySummary\', 45, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(33, 19, \'May 1 - Day 5: 10 miles\', \'Stop for the night. \', 1, \'FitDistanceDailySummary\', 55, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(35, 19, \'May 2 - Day 6: 10 miles\', \'The Old Forest is now quite close.\', 1, \'FitDistanceDailySummary\', 61, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(36, 19, \'May 2 - Day 6: 10 miles\', \'Camp next to the Road.\', 1, \'FitDistanceDailySummary\', 65, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(38, 19, \'May 3 - Day 7: 10 miles\', \'To the south the Old Forest ends.\', 1, \'FitDistanceDailySummary\', 69, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(39, 19, \'May 3 - Day 7: 10 miles\', \'Camp.\', 1, \'FitDistanceDailySummary\', 75, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(41, 19, \'May 4 - Day 8: 10 miles\', \'Barrow-downs continue. Between the Road and the Downs is a hedge.\', 1, \'FitDistanceDailySummary\', 81, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(42, 19, \'May 4 - Day 8: 10 miles\', \'Reach Bree. Stay at The Prancing Pony. Spend a late evening.\', 1, \'FitDistanceDailySummary\', 85, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(44, 19, \'May 5 - Day 9: 6 miles\', \'Reach the South-gate of Bree. Leave village and ride east on the road.\', 1, \'FitDistanceDailySummary\', 86, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(45, 19, \'May 5 - Day 9: 6 miles\', \'Camp next to a trail leading north into the Chetwood on Bree-hill.\', 1, \'FitDistanceDailySummary\', 91, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(47, 19, \'May 6 - Day 10: 10 miles\', \'Continue downhill along road next to the heart of the Chetwood.\', 1, \'FitDistanceDailySummary\', 96, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(48, 19, \'May 6 - Day 10: 10 miles\', \'Stay at The Forsaken Inn: the last inn along the Great East Road.\', 1, \'FitDistanceDailySummary\', 101, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(50, 19, \'May 7 - Day 11: 12 miles\', \'Reach the east edge of the Chetwood. Scattered farms to the south.\', 1, \'FitDistanceDailySummary\', 107, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(51, 19, \'May 7 - Day 11: 12 miles\', \'Begin to see the Midgewater Marshes off to the northeast.\', 1, \'FitDistanceDailySummary\', 111, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(52, 19, \'May 7 - Day 11: 12 miles\', \'Camp.\', 1, \'FitDistanceDailySummary\', 113, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(54, 19, \'May 8 - Day 12: 12 miles\', \'Road runs gently downhill.\', 1, \'FitDistanceDailySummary\', 117, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(55, 19, \'May 8 - Day 12: 12 miles\', \'Due north lie the western edge of the Midgewater Marshes.\', 1, \'FitDistanceDailySummary\', 125, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(57, 19, \'May 9 - Day 13: 12 miles\', \'Meet a traveller who hurries by.\', 1, \'FitDistanceDailySummary\', 133, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(58, 19, \'May 9 - Day 13: 12 miles\', \'Camp. Marshes to the north are now closer to the road.\', 1, \'FitDistanceDailySummary\', 137, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(60, 19, \'May 10 - Day 14: 12 miles\', \'Road curves more to the east. Marshes fill all the northern horizon.\', 1, \'FitDistanceDailySummary\', 143, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(61, 19, \'May 10 - Day 14: 12 miles\', \'Camp due south of the Midgewater Marshes.\', 1, \'FitDistanceDailySummary\', 149, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(63, 19, \'May 11 - Day 15: 11 miles\', \'Road continues east around southern edge of the Marshes.\', 1, \'FitDistanceDailySummary\', 155, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(64, 19, \'May 11 - Day 15: 11 miles\', \'Camp south of the road, away from the Marshes.\', 1, \'FitDistanceDailySummary\', 160, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(66, 19, \'May 12 - Day 16: 10 miles\', \'Road now turns slightly more to the north.\', 1, \'FitDistanceDailySummary\', 165, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(67, 19, \'May 12 - Day 16: 10 miles\', \'Reach the southeast tip of the Midgewater Marshes. Camp.\', 1, \'FitDistanceDailySummary\', 170, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(69, 19, \'May 13 - Day 17: 10 miles\', \'Weathertop is visible ahead, with the Weather Hills to its north.\', 1, \'FitDistanceDailySummary\', 175, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(70, 19, \'May 13 - Day 17: 10 miles\', \'Camp. Open, rough lands on both sides of the road. Weather pleasant.\', 1, \'FitDistanceDailySummary\', 180, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(72, 19, \'May 14 - Day 18: 10 miles\', \'Land now slopes slowly uphill toward Weathertop.\', 1, \'FitDistanceDailySummary\', 185, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(73, 19, \'May 14 - Day 18: 10 miles\', \'Weathertop rises more clearly ahead. Camp.\', 1, \'FitDistanceDailySummary\', 190, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(75, 19, \'May 15 - Day 19: 10 miles\', \'Weathertop looms ahead and fills all the view.\', 1, \'FitDistanceDailySummary\', 195, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(76, 19, \'May 15 - Day 19: 10 miles\', \'Weathertop rises immediately north of the Road. Camp at its foot.\', 1, \'FitDistanceDailySummary\', 200, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(78, 19, \'May 16 - Day 20: 10 miles\', \'Road reaches southeastern foot of Weathertop.\', 1, \'FitDistanceDailySummary\', 205, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(79, 19, \'May 16 - Day 20: 10 miles\', \'Come to the last foothill below Weathertop. Camp at its foot.\', 1, \'FitDistanceDailySummary\', 210, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(81, 19, \'May 17 - Day 21: 9 miles\', \'South of the road are more rugged areas, covered with thickets.\', 1, \'FitDistanceDailySummary\', 215, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(82, 19, \'May 17 - Day 21: 9 miles\', \'Camp. Weather still is warm and clear.\', 1, \'FitDistanceDailySummary\', 219, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(84, 19, \'May 18 - Day 22: 9 miles\', \'Camp.\', 1, \'FitDistanceDailySummary\', 228, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(85, 19, \'May 19 - Day 23: 9 miles\', \'Road deteriorates still more. Weathertop now appears lower. Camp. \', 1, \'FitDistanceDailySummary\', 237, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(86, 19, \'May 20 - Day 24: 9 miles\', \'Road turns more east. Weathertop is no longer straight behind.\', 1, \'FitDistanceDailySummary\', 242, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(87, 19, \'May 20 - Day 24: 9 miles\', \'Continue east. Camp. Weather Hills still visible on the western horizon.\', 1, \'FitDistanceDailySummary\', 246, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(88, 19, \'May 21 - Day 25: 9 miles\', \'Continue east on the Road. Camp.\', 1, \'FitDistanceDailySummary\', 255, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(89, 19, \'May 22 - Day 26: 9 miles\', \'Continue east. Far behind, the Weather Hills show less and less. Ahead, company begins to see the tops of the Trollshaws. Camp.\', 1, \'FitDistanceDailySummary\', 264, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(90, 19, \'May 23 - Day 27: 9 miles\', \'Continue east. Road is rough. Camp.\', 1, \'FitDistanceDailySummary\', 273, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(91, 19, \'May 24 - Day 28: 9 miles\', \'Continue east. Behind, Weather Hills have almost disappeared. Ahead, dark wooded hills appear higher. Camp.\', 1, \'FitDistanceDailySummary\', 282, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(92, 19, \'May 25 - Day 29: 9 miles\', \'Open country. No streams. Camp. \', 1, \'FitDistanceDailySummary\', 291, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(93, 19, \'May 26 - Day 30: 9 miles\', \'Road curves more to southeast. South of the road are bushes and stunted trees: wild and pathless. Camp north of the road.\', 1, \'FitDistanceDailySummary\', 300, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(94, 19, \'May 27 - Day 31: 9 miles\', \'Weather pleasant. Ahead, Trollshaws show clearly. Camp.\', 1, \'FitDistanceDailySummary\', 309, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(95, 19, \'May 28 - Day 32: 8 miles\', \'Road swings southeast through open country. Ahead on hills of the Trollshaws, can now see “old castles with an evil look.” Camp.\', 1, \'FitDistanceDailySummary\', 317, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(97, 19, \'May 29 - Day 33: 15 miles\', \'Reach northeast end of a valley south of the road. Continue on. Road now is only a muddy track.\', 1, \'FitDistanceDailySummary\', 323, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(98, 19, \'May 29 - Day 33: 15 miles\', \'Nearly dark: cross The Last Bridge. Stop to camp in clump of trees. Realize Gandalf is missing. Pony bolts into river and loses baggage with food. See fire in trees on hillside to east. Decide to investigate.\', 1, \'FitDistanceDailySummary\', 325, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(99, 19, \'May 29 - Day 33: 15 miles\', \'No path through trees to fire. Reach fire: Trolls. Company captured. (Note: This location is the most difficult of the entire journey to reconcile with LOTR. While logically, the Company probably went a mile or two at most, this does not work at all with the LOTR pathways. Even at a minimum, it should be at least 7 miles from the bridge to the Trolls. See detailed discussion and maps in The Atlas).\', 1, \'FitDistanceDailySummary\', 332, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(101, 19, \'May 30 - Day 34: 12 miles\', \'Gandalf and company follow trail to Troll-hole. Find swords for Thorin and Gandalf. Bilbo takes blade. Collect gold. Return to fire.\', 1, \'FitDistanceDailySummary\', 334, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(102, 19, \'May 30 - Day 34: 12 miles\', \'Continue down path and reach the Road. Bury gold and continue east.\', 1, \'FitDistanceDailySummary\', 339, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(103, 19, \'May 30 - Day 34: 12 miles\', \'Road now turns southeast. Skirts hills of the Trollshaws.\', 1, \'FitDistanceDailySummary\', 340, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(104, 19, \'May 30 - Day 34: 12 miles\', \'Camp.\', 1, \'FitDistanceDailySummary\', 344, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(106, 19, \'June 1 - Day 35: 16 miles\', \'Enter woods. Continue east on road through woods.\', 1, \'FitDistanceDailySummary\', 348, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(107, 19, \'June 1 - Day 35: 16 miles\', \'Woods run into the valley on north.\', 1, \'FitDistanceDailySummary\', 350, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(108, 19, \'June 1 - Day 35: 16 miles\', \'Pass out-thrust toe of a hill.\', 1, \'FitDistanceDailySummary\', 355, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(109, 19, \'June 1 - Day 35: 16 miles\', \'Camp near valley from the north.\', 1, \'FitDistanceDailySummary\', 360, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(111, 19, \'June 2 - Day 36: 13 miles\', \'Can see ruins on hilltop to the north.\', 1, \'FitDistanceDailySummary\', 365, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(112, 19, \'June 2 - Day 36: 13 miles\', \'South of the Road, ravine of the Bruinen comes close. Road turns NE.\', 1, \'FitDistanceDailySummary\', 368, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(113, 19, \'June 2 - Day 36: 13 miles\', \'Pass a valley from the north.\', 1, \'FitDistanceDailySummary\', 370, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(114, 19, \'June 2 - Day 36: 13 miles\', \'Camp.\', 1, \'FitDistanceDailySummary\', 373, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(116, 19, \'June 3 - Day 37: 16 miles\', \'Cross a small stream. Road bends more northeast. The steep ravine of the Bruinen also runs east-northeast not far to the south.\', 1, \'FitDistanceDailySummary\', 378, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(117, 19, \'June 3 - Day 37: 16 miles\', \'Continue east-northeast along road . Ride very quickly.\', 1, \'FitDistanceDailySummary\', 380, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(118, 19, \'June 3 - Day 37: 16 miles\', \'Road runs gently downhill. Much grass on sides. Hills still on the north.\', 1, \'FitDistanceDailySummary\', 385, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(119, 19, \'June 3 - Day 37: 16 miles\', \'Road drops through a cutting of red stone topped with tall pines, then crosses a long flat mile toward the river.\', 1, \'FitDistanceDailySummary\', 388, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(120, 19, \'June 3 - Day 37: 16 miles\', \'Reach the Ford of Bruinen. Can see Misty Mountains clearly. Camp. \', 1, \'FitDistanceDailySummary\', 389, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(122, 19, \'June 4 - Day 38: 8 miles\', \'Gandalf searches out the path. Must go carefully.\', 1, \'FitDistanceDailySummary\', 390, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(123, 19, \'June 4 - Day 38: 8 miles\', \'Path runs next to a steep gully on the left.\', 1, \'FitDistanceDailySummary\', 392, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(124, 19, \'June 4 - Day 38: 8 miles\', \'On the east, a deep ravine holds a waterfall.\', 1, \'FitDistanceDailySummary\', 394, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(125, 19, \'June 4 - Day 38: 8 miles\', \'Come to the sudden cliff into the valley of Rivendell. Ride slowly down the zig-zag path. Pines cling to the upper slopes, beech and oak below.\', 1, \'FitDistanceDailySummary\', 396, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(126, 19, \'June 4 - Day 38: 8 miles\', \'Lead the ponies across the narrow bridge over the upper Bruinen. Reach the Last Homely House in Rivendell.\', 1, \'FitDistanceDailySummary\', 397, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(127, 18, \'Rivendell to the Lonely Mountain\', \'\', 1, \'FitDistanceDailySummary\', 967, \'flow\', NULL, 10, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(128, 127, \'Midyear’s Day - Day 1: 4 miles\', \'Leave Rivendell, camp in foothills - Ponies.\', 1, \'FitDistanceDailySummary\', 401, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(129, 127, \'2 Lithe - Day 2: 4 miles\', \'Still in foothills - Ponies.\', 1, \'FitDistanceDailySummary\', 405, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(130, 127, \'July 1-15 - Day 3-17: 4 miles per day = 68 miles\', \'Climbing steadily - Ponies. 4 miles per day\', 1, \'FitDistanceDailySummary\', 465, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(131, 127, \'July 16 - Day 18: 4 miles\', \'Tremendous Thunder-battle in afternoon. Shelter in a cave. Goblins capture them.\', 1, \'FitDistanceDailySummary\', 469, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(132, 127, \'July 17/18/19 - Nt/Day/Nt/Day 19/20/a.m.21: 26 miles\', \'Thorin questioned in Great Goblin’s Cavern. Gandalf rescues them.\', 1, \'FitDistanceDailySummary\', 474, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(133, 127, \'July 17/18/19 - Nt/Day/Nt/Day 19/20/a.m.21: 26 miles\', \'Company pauses. Gandalf makes light. Dwarves carry Bilbo.\', 1, \'FitDistanceDailySummary\', 476, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(134, 127, \'July 17/18/19 - Nt/Day/Nt/Day 19/20/a.m.21: 26 miles\', \'Gandalf and Thorin fight off Goblins.\', 1, \'FitDistanceDailySummary\', 482, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(135, 127, \'July 17/18/19 - Nt/Day/Nt/Day 19/20/a.m.21: 26 miles\', \'Goblins Attack. \', 1, \'FitDistanceDailySummary\', 494, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(136, 127, \'July 17/18/19 - Nt/Day/Nt/Day 19/20/a.m.21: 26 miles\', \'End of Goblin attack. In rush, Bilbo accidentally left behind.\', 1, \'FitDistanceDailySummary\', 495, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(138, 127, \'Bilbo Continues Alone: 11 miles\', \'Bilbo, while crawling, finds the ONE RING. Pulling out his sword for light, he begins trotting along passage.\', 1, \'FitDistanceDailySummary\', 496, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(139, 127, \'Bilbo Continues Alone: 11 miles\', \'Bilbo unknowingly passes passage to ‘Back Door’\', 1, \'FitDistanceDailySummary\', 503.5, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(140, 127, \'Bilbo Continues Alone: 11 miles\', \'Bilbo reaches Gollum’s Lake. Riddle Contest.\', 1, \'FitDistanceDailySummary\', 504.5, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(141, 127, \'Bilbo Continues Alone: 11 miles\', \'Bilbo follows Gollum back up passage. Side passages begin to appear.\', 1, \'FitDistanceDailySummary\', 505, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(142, 127, \'Bilbo Continues Alone: 11 miles\', \'Gollum blocks entrance to tunnel to Back Door. Bilbo jumps over him.\', 1, \'FitDistanceDailySummary\', 505.5, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(143, 127, \'Bilbo Continues Alone: 11 miles\', \'Bilbo reaches the ‘Back Door’ and escapes.\', 1, \'FitDistanceDailySummary\', 506, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(145, 127, \'Continuing July 19 - Day 21: 41 miles\', \'Bilbo leaves upland valley on trail with cliff on left, drop-off on right.\', 1, \'FitDistanceDailySummary\', 514, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(146, 127, \'Continuing July 19 - Day 21: 41 miles\', \'Bilbo finds Dwarves and Gandalf in dell below trail. They continue on.\', 1, \'FitDistanceDailySummary\', 518, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(147, 127, \'Continuing July 19 - Day 21: 41 miles\', \'Cross a stream (based on atlas map).\', 1, \'FitDistanceDailySummary\', 522, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(148, 127, \'Continuing July 19 - Day 21: 41 miles\', \'Trail leads to top of a landslide. They slip sideways in the stones.\', 1, \'FitDistanceDailySummary\', 526, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(149, 127, \'Continuing July 19 - Day 21: 41 miles\', \'Reach the bottom of the landslide and go east. Pine forest (ca. 7 p.m.).\', 1, \'FitDistanceDailySummary\', 527, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(150, 127, \'Continuing July 19 - Day 21: 41 miles\', \'Clearing. Hear wolves. Climb trees. WARG ATTACK. Eagles Rescue.\', 1, \'FitDistanceDailySummary\', 535, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(151, 127, \'Continuing July 19 - Day 21: 41 miles\', \'Eagles take Company to their Eyrie. Sleep there.\', 1, \'FitDistanceDailySummary\', 547, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(152, 127, \'July 20 - Day 22: 58 miles\', \'Eagles fly Company to The Carrock.\', 1, \'FitDistanceDailySummary\', 599, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(153, 127, \'July 20 - Day 22: 58 miles\', \'Company reaches Beorn’s house.\', 1, \'FitDistanceDailySummary\', 605, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(155, 127, \'July 22 - Day 24: 9 miles\', \'Camp.\', 1, \'FitDistanceDailySummary\', 614, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(156, 127, \'July 23 - Day 25: 20 miles\', \'Ride through grasslands west of Mirkwood.\', 1, \'FitDistanceDailySummary\', 634, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(157, 127, \'July 24 - Day 26: 25 miles\', \'Bright, fair, chill fall-like mist. Bilbo sees Beorn. Press on under moon.\', 1, \'FitDistanceDailySummary\', 649, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(158, 127, \'July 25 - Day 27: 18 miles\', \'Start before dawn. Land slopes up as they near the forest. Reach trees in afternoon. Camp.\', 1, \'FitDistanceDailySummary\', 667, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(160, 127, \'July 26 - Day 28 (Day 1 in Mirkwood): 8 miles\', \'First camp.\', 1, \'FitDistanceDailySummary\', 675, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(161, 127, \'July 27\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 684, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(162, 127, \'July 28\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 691, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(163, 127, \'July 29\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 698, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(164, 127, \'July 30\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 705, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(165, 127, \'July 31\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 712, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(166, 127, \'August 1\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 719, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(167, 127, \'August 2\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 726, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(168, 127, \'August 3\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 733, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(169, 127, \'August 4\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 740, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(170, 127, \'August 5\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 747, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(171, 127, \'August 6\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 754, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(172, 127, \'August 7\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 761, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(173, 127, \'August 8\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 768, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(174, 127, \'August 9\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 775, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(175, 127, \'August 10\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 782, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(176, 127, \'August 11\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 789, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(177, 127, \'August 12\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 796, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(178, 127, \'August 13\', \'Day 2 On Forest Trail in Mirkwood.\', 1, \'FitDistanceDailySummary\', 803, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(179, 127, \'Aug. 16 - Day 48 (Day 21 in Mirkwood): 5 miles\', \'After 143 miles on Forest Trail, reach the Enchanted River. Cross by boat. Bombur falls in water and immediately falls asleep and must be carried.\', 1, \'FitDistanceDailySummary\', 810, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(180, 127, \'Aug. 16 - Day 48 (Day 21 in Mirkwood): 5 miles\', \'Camp.\', 1, \'FitDistanceDailySummary\', 813, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(181, 127, \'Aug. 17 - Day 49 (Day 22 in Mirkwood): 6 miles\', \'On Forest Trail.\', 1, \'FitDistanceDailySummary\', 819, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(182, 127, \'Aug. 18 - Day 50 (Day 23 in Mirkwood): 6 miles\', \'On Forest Trail.\', 1, \'FitDistanceDailySummary\', 825, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(183, 127, \'Aug. 19 - Day 51 (Day 24 in Mirkwood): 6 miles\', \'Walk through open beech-woods much of the day.\', 1, \'FitDistanceDailySummary\', 831, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(184, 127, \'Aug. 20 - Day 52 (Day 25 in Mirkwood): 6 miles\', \'Bilbo climbs a tree (in an oak-wood), but can see nothing as it is in bottom of a bowl.\', 1, \'FitDistanceDailySummary\', 835, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(185, 127, \'Aug. 20 - Day 52 (Day 25 in Mirkwood): 6 miles\', \'Camp. Eat last of food at supper.\', 1, \'FitDistanceDailySummary\', 837, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(187, 127, \'Aug. 21 - Day 53 (Day 26 in Mirkwood): 7 miles\', \'About to camp when see a fire. LEAVE PATH.\', 1, \'FitDistanceDailySummary\', 843, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(188, 127, \'Aug. 21 - Day 53 (Day 26 in Mirkwood): 7 miles\', \'Reach first Elves’ fire. Lights put out. Frantically search for each other.\', 1, \'FitDistanceDailySummary\', 843.5, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(189, 127, \'Aug. 21 - Day 53 (Day 26 in Mirkwood): 7 miles\', \'Reach second Elves’ fire. Lights put out again. Stay huddled together.\', 1, \'FitDistanceDailySummary\', 844, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(191, 127, \'Aug. 22 - Day 54 (Day 27 in Mirkwood): 4 miles\', \'Lights put out. Thorin captured by Elves. Dwarves scatter and are captured by spiders. Bilbo left alone. Kills spider. Swoons. Wakes during the morning and names his sword: Sting.\', 1, \'FitDistanceDailySummary\', 844.5, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(192, 127, \'Aug. 22 - Day 54 (Day 27 in Mirkwood): 4 miles\', \'Bilbo finds dwarves cocooned and rescues them.\', 1, \'FitDistanceDailySummary\', 846, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(193, 127, \'Aug. 22 - Day 54 (Day 27 in Mirkwood): 4 miles\', \'Bilbo draws spiders away. Dwarves almost recaptured. Bilbo returns. \', 1, \'FitDistanceDailySummary\', 847, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(194, 127, \'Aug. 22 - Day 54 (Day 27 in Mirkwood): 4 miles\', \'After more fighting against spiders, spiders give up. Camp there.\', 1, \'FitDistanceDailySummary\', 848, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(195, 127, \'Aug. 23 - Day 55 (Day 28 in Mirkwood): 7 miles\', \'Struggle on all day. Dwarves surrender when surrounded by Elves. Bilbo uses ring and disappears.\', 1, \'FitDistanceDailySummary\', 853, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(196, 127, \'Aug. 23 - Day 55 (Day 28 in Mirkwood): 7 miles\', \'Dwarves imprisoned in Elvenking Thranduil’s Caverns. Bilbo follows\', 1, \'FitDistanceDailySummary\', 855, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(198, 127, \'Sept. 21 - Day 83: 14 miles\', \'Leave trees of Mirkwood.\', 1, \'FitDistanceDailySummary\', 857, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(199, 127, \'Sept. 21 - Day 83: 14 miles\', \'Dusk - Reach huts of the Raft-elves. Dwarves still in barrels. Bilbo huddles nearby wearing Ring.\', 1, \'FitDistanceDailySummary\', 869, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(201, 127, \'Sept. 22 - Day 84: 32 miles\', \'Reach marshy area. Bilbo sees the Lonely Mountain.\', 1, \'FitDistanceDailySummary\', 879, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(202, 127, \'Sept. 22 - Day 84: 32 miles\', \'End of the marshes. River rushes along.\', 1, \'FitDistanceDailySummary\', 899, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(203, 127, \'Sept. 22 - Day 84: 32 miles\', \'Reach Lake-town after sunset. Bilbo frees dwarves. They enter the town.\', 1, \'FitDistanceDailySummary\', 901, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(205, 127, \'Oct. 9 - Day 101: 5 miles\', \'Camp at the mouth of the River Running.\', 1, \'FitDistanceDailySummary\', 906, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(206, 127, \'Oct. 10 - Day 102: 5 miles\', \'Row upstream against current. Camp.\', 1, \'FitDistanceDailySummary\', 911, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(207, 127, \'Oct. 11 - Day 103: 5 miles\', \'Third day in boats. Camp on west shore. Met there by ponies and supplies.\', 1, \'FitDistanceDailySummary\', 916, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(209, 127, \'Oct. 12 - Day 104: 13 miles\', \'Reach the Desolation of the Dragon.\', 1, \'FitDistanceDailySummary\', 921, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(210, 127, \'Oct. 12 - Day 104: 13 miles\', \'1st Camp - West of the south tip of the west spur of Lonely Mountain.\', 1, \'FitDistanceDailySummary\', 929, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(211, 127, \'Oct. 13 - Day 105: 7 miles\', \'Bilbo, Fili, Kili and Balin scout to River Running and go part way toward the Front Gate.\', 1, \'FitDistanceDailySummary\', 936, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(212, 127, \'Oct. 14 - Day 106: 5 miles\', \'Move to 2nd camp - In narrower valley due west of the mountain. \', 1, \'FitDistanceDailySummary\', 941, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(213, 127, \'Oct. 19 - Day 111: 1 mile\', \'Move to upland bay by Hidden Door = 3rd Camp.\', 1, \'FitDistanceDailySummary\', 942, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(215, 127, \'Oct. 30 - Day 122: 4 miles Durin’s Day!\', \'Bilbo descends to Smaug’s Cellar, steals cup, returns. Smaug searches ANGRILY.\', 1, \'FitDistanceDailySummary\', 946, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(216, 127, \'Nov. 1 - Day 123: 4 miles\', \'Bilbo returns to Smaug’s Cellar. Riddles. \', 1, \'FitDistanceDailySummary\', 948, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(217, 127, \'Nov. 1 - Day 123: 4 miles\', \'After Bilbo returns, company moves into passage. Smaug smashes mountainside, then attacks Lake-town. Bard kills Smaug.\', 1, \'FitDistanceDailySummary\', 950, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(218, 127, \'Nov. 2 - Day 124: 9 miles\', \'Company goes to Smaug’s Cellar. Bilbo finds Arkenstone. Dwarves search hoard.\', 1, \'FitDistanceDailySummary\', 952, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(219, 127, \'Nov. 2 - Day 124: 9 miles\', \'Go through Chamber of Thror.\', 1, \'FitDistanceDailySummary\', 954.5, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(220, 127, \'Nov. 2 - Day 124: 9 miles\', \'Leave via Front Gate of the mountain.\', 1, \'FitDistanceDailySummary\', 955, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(221, 127, \'Nov. 2 - Day 124: 9 miles\', \'Ford stream at the fallen bridge. Take the road into the valley of Dale.\', 1, \'FitDistanceDailySummary\', 955.5, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(222, 127, \'Nov. 2 - Day 124: 9 miles\', \'Take a hill path from the road onto the western spur of the mountain.\', 1, \'FitDistanceDailySummary\', 956.5, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(223, 127, \'Nov. 2 - Day 124: 9 miles\', \'Path reaches steep drop-off.\', 1, \'FitDistanceDailySummary\', 958, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(224, 127, \'Nov. 2 - Day 124: 9 miles\', \'Path ends at Guard-post. 4th Camp.\', 1, \'FitDistanceDailySummary\', 959, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(225, 127, \'Nov. 3 - Day 125: 4 miles\', \'They learn of Smaug’s death and return to Front Gate. Fortify entrance. \', 1, \'FitDistanceDailySummary\', 963, NULL, NULL, 5, 12)');
        $this->addSql('INSERT INTO rpg_challenge_global VALUES(226, 127, \'Nov. 22 - Day 144: 4 miles\', \'Bilbo goes to Bard’s camp and gives him the Arkenstone. Returns to the gate of the Lonely Mountain.\', 1, \'FitDistanceDailySummary\', 967, NULL, NULL, 5, 12)');

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
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 18');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 19');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 20');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 21');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 22');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 23');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 24');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 25');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 27');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 29');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 31');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 33');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 35');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 36');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 38');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 39');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 41');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 42');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 44');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 45');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 47');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 48');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 50');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 51');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 52');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 54');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 55');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 57');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 58');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 60');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 61');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 63');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 64');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 66');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 67');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 69');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 70');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 72');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 73');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 75');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 76');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 78');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 79');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 81');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 82');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 84');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 85');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 86');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 87');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 88');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 89');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 90');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 91');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 92');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 93');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 94');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 95');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 97');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 98');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 99');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 101');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 102');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 103');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 104');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 106');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 107');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 108');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 109');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 111');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 112');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 113');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 114');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 116');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 117');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 118');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 119');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 120');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 122');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 123');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 124');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 125');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 126');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 127');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 128');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 129');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 130');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 131');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 132');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 133');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 134');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 135');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 136');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 138');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 139');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 140');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 141');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 142');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 143');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 145');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 146');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 147');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 148');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 149');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 150');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 151');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 152');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 153');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 155');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 156');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 157');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 158');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 160');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 161');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 162');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 163');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 164');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 165');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 166');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 167');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 168');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 169');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 170');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 171');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 172');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 173');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 174');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 175');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 176');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 177');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 178');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 179');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 180');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 181');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 182');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 183');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 184');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 185');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 187');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 188');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 189');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 191');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 192');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 193');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 194');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 195');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 196');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 198');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 199');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 201');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 202');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 203');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 205');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 206');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 207');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 209');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 210');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 211');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 212');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 213');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 215');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 216');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 217');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 218');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 219');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 220');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 221');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 222');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 223');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 224');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 225');
        $this->addSql('DELETE FROM rpg_challenge_global WHERE id = 226');
    }
}
