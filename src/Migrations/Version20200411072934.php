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
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200411072934 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE api_access_log ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE body_composition ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE body_fat ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE body_weight ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE consume_caffeine ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE consume_water ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE contribution_license ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE exercise ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE exercise_summary ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE exercise_type ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE fit_calories_daily_summary ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE fit_distance_daily_summary ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE fit_floors_intra_day ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE fit_steps_daily_summary ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE fit_steps_intra_day ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE food_database ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE food_diary ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE food_meals ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE food_nutrition ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE part_of_day ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE patient ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE patient_credentials ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE patient_device ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE patient_friends ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE patient_goals ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE patient_membership ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE patient_settings ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rpg_challenge_friends ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rpg_challenge_global ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rpg_challenge_global_patient ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rpg_indicator ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rpg_milestones ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rpg_rewards ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rpg_rewards_awarded ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rpg_xp ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE site_nav_item ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE site_news ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE sync_queue ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE third_party_service ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tracking_device ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE unit_of_measurement ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE uploaded_file ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE workout_categories ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE workout_equipment ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE workout_exercise ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE workout_muscle ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE workout_muscle_relation ADD guid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE api_access_log DROP guid');
        $this->addSql('ALTER TABLE body_composition DROP guid');
        $this->addSql('ALTER TABLE body_fat DROP guid');
        $this->addSql('ALTER TABLE body_weight DROP guid');
        $this->addSql('ALTER TABLE consume_caffeine DROP guid');
        $this->addSql('ALTER TABLE consume_water DROP guid');
        $this->addSql('ALTER TABLE contribution_license DROP guid');
        $this->addSql('ALTER TABLE exercise DROP guid');
        $this->addSql('ALTER TABLE exercise_summary DROP guid');
        $this->addSql('ALTER TABLE exercise_type DROP guid');
        $this->addSql('ALTER TABLE fit_calories_daily_summary DROP guid');
        $this->addSql('ALTER TABLE fit_distance_daily_summary DROP guid');
        $this->addSql('ALTER TABLE fit_floors_intra_day DROP guid');
        $this->addSql('ALTER TABLE fit_steps_daily_summary DROP guid');
        $this->addSql('ALTER TABLE fit_steps_intra_day DROP guid');
        $this->addSql('ALTER TABLE food_database DROP guid');
        $this->addSql('ALTER TABLE food_diary DROP guid');
        $this->addSql('ALTER TABLE food_meals DROP guid');
        $this->addSql('ALTER TABLE food_nutrition DROP guid');
        $this->addSql('ALTER TABLE part_of_day DROP guid');
        $this->addSql('ALTER TABLE patient DROP guid');
        $this->addSql('ALTER TABLE patient_credentials DROP guid');
        $this->addSql('ALTER TABLE patient_device DROP guid');
        $this->addSql('ALTER TABLE patient_friends DROP guid');
        $this->addSql('ALTER TABLE patient_goals DROP guid');
        $this->addSql('ALTER TABLE patient_membership DROP guid');
        $this->addSql('ALTER TABLE patient_settings DROP guid');
        $this->addSql('ALTER TABLE rpg_challenge_friends DROP guid');
        $this->addSql('ALTER TABLE rpg_challenge_global DROP guid');
        $this->addSql('ALTER TABLE rpg_challenge_global_patient DROP guid');
        $this->addSql('ALTER TABLE rpg_indicator DROP guid');
        $this->addSql('ALTER TABLE rpg_milestones DROP guid');
        $this->addSql('ALTER TABLE rpg_rewards DROP guid');
        $this->addSql('ALTER TABLE rpg_rewards_awarded DROP guid');
        $this->addSql('ALTER TABLE rpg_xp DROP guid');
        $this->addSql('ALTER TABLE site_nav_item DROP guid');
        $this->addSql('ALTER TABLE site_news DROP guid');
        $this->addSql('ALTER TABLE sync_queue DROP guid');
        $this->addSql('ALTER TABLE third_party_service DROP guid');
        $this->addSql('ALTER TABLE tracking_device DROP guid');
        $this->addSql('ALTER TABLE unit_of_measurement DROP guid');
        $this->addSql('ALTER TABLE uploaded_file DROP guid');
        $this->addSql('ALTER TABLE workout_categories DROP guid');
        $this->addSql('ALTER TABLE workout_equipment DROP guid');
        $this->addSql('ALTER TABLE workout_exercise DROP guid');
        $this->addSql('ALTER TABLE workout_muscle DROP guid');
        $this->addSql('ALTER TABLE workout_muscle_relation DROP guid');

    }
}
