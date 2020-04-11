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
final class Version20200411092158 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_28D549012B6FCFB2 ON api_access_log (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C5EB24472B6FCFB2 ON body_composition (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_190FCCF42B6FCFB2 ON body_fat (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92D1F6472B6FCFB2 ON body_weight (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2152F8362B6FCFB2 ON consume_caffeine (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_11CA89992B6FCFB2 ON consume_water (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_164984D82B6FCFB2 ON contribution_license (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AEDAD51C2B6FCFB2 ON exercise (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_143D91262B6FCFB2 ON exercise_summary (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D5FB359B2B6FCFB2 ON exercise_type (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_53EEB7012B6FCFB2 ON fit_calories_daily_summary (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E797247D2B6FCFB2 ON fit_distance_daily_summary (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_60A6DB352B6FCFB2 ON fit_floors_intra_day (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DE1CE7DF2B6FCFB2 ON fit_steps_daily_summary (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8852F152B6FCFB2 ON fit_steps_intra_day (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E11E216C2B6FCFB2 ON food_database (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_601A28082B6FCFB2 ON food_diary (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_134823002B6FCFB2 ON food_meals (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2F390D6A2B6FCFB2 ON food_nutrition (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EE0DB1222B6FCFB2 ON part_of_day (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1ADAD7EB2B6FCFB2 ON patient (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CA0617412B6FCFB2 ON patient_credentials (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2615F742B6FCFB2 ON patient_device (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C8D25822B6FCFB2 ON patient_friends (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5212C5E02B6FCFB2 ON patient_goals (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_303F746A2B6FCFB2 ON patient_membership (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D2C0F8602B6FCFB2 ON patient_settings (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5BDB23D2B6FCFB2 ON rpg_challenge_friends (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6479702B6FCFB2 ON rpg_challenge_global (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA07E8632B6FCFB2 ON rpg_challenge_global_patient (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F75BD9DA2B6FCFB2 ON rpg_indicator (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CBF373C2B6FCFB2 ON rpg_milestones (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FBCA13082B6FCFB2 ON rpg_rewards (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A069EAE62B6FCFB2 ON rpg_rewards_awarded (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7808C9532B6FCFB2 ON rpg_xp (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8A8D9AF2B6FCFB2 ON site_nav_item (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_264924A92B6FCFB2 ON site_news (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99CCF5CE2B6FCFB2 ON sync_queue (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B4645ADE2B6FCFB2 ON third_party_service (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A1A335C22B6FCFB2 ON tracking_device (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DC68A6042B6FCFB2 ON unit_of_measurement (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B40DF75D2B6FCFB2 ON uploaded_file (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_56D709E22B6FCFB2 ON workout_categories (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F1E2B2D72B6FCFB2 ON workout_equipment (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_76AB38AA2B6FCFB2 ON workout_exercise (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E99206212B6FCFB2 ON workout_muscle (guid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_56B0B0112B6FCFB2 ON workout_muscle_relation (guid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP INDEX UNIQ_28D549012B6FCFB2 ON api_access_log');
        $this->addSql('DROP INDEX UNIQ_C5EB24472B6FCFB2 ON body_composition');
        $this->addSql('DROP INDEX UNIQ_190FCCF42B6FCFB2 ON body_fat');
        $this->addSql('DROP INDEX UNIQ_92D1F6472B6FCFB2 ON body_weight');
        $this->addSql('DROP INDEX UNIQ_2152F8362B6FCFB2 ON consume_caffeine');
        $this->addSql('DROP INDEX UNIQ_11CA89992B6FCFB2 ON consume_water');
        $this->addSql('DROP INDEX UNIQ_164984D82B6FCFB2 ON contribution_license');
        $this->addSql('DROP INDEX UNIQ_AEDAD51C2B6FCFB2 ON exercise');
        $this->addSql('DROP INDEX UNIQ_143D91262B6FCFB2 ON exercise_summary');
        $this->addSql('DROP INDEX UNIQ_D5FB359B2B6FCFB2 ON exercise_type');
        $this->addSql('DROP INDEX UNIQ_53EEB7012B6FCFB2 ON fit_calories_daily_summary');
        $this->addSql('DROP INDEX UNIQ_E797247D2B6FCFB2 ON fit_distance_daily_summary');
        $this->addSql('DROP INDEX UNIQ_60A6DB352B6FCFB2 ON fit_floors_intra_day');
        $this->addSql('DROP INDEX UNIQ_DE1CE7DF2B6FCFB2 ON fit_steps_daily_summary');
        $this->addSql('DROP INDEX UNIQ_D8852F152B6FCFB2 ON fit_steps_intra_day');
        $this->addSql('DROP INDEX UNIQ_E11E216C2B6FCFB2 ON food_database');
        $this->addSql('DROP INDEX UNIQ_601A28082B6FCFB2 ON food_diary');
        $this->addSql('DROP INDEX UNIQ_134823002B6FCFB2 ON food_meals');
        $this->addSql('DROP INDEX UNIQ_2F390D6A2B6FCFB2 ON food_nutrition');
        $this->addSql('DROP INDEX UNIQ_EE0DB1222B6FCFB2 ON part_of_day');
        $this->addSql('DROP INDEX UNIQ_1ADAD7EB2B6FCFB2 ON patient');
        $this->addSql('DROP INDEX UNIQ_CA0617412B6FCFB2 ON patient_credentials');
        $this->addSql('DROP INDEX UNIQ_2615F742B6FCFB2 ON patient_device');
        $this->addSql('DROP INDEX UNIQ_7C8D25822B6FCFB2 ON patient_friends');
        $this->addSql('DROP INDEX UNIQ_5212C5E02B6FCFB2 ON patient_goals');
        $this->addSql('DROP INDEX UNIQ_303F746A2B6FCFB2 ON patient_membership');
        $this->addSql('DROP INDEX UNIQ_D2C0F8602B6FCFB2 ON patient_settings');
        $this->addSql('DROP INDEX UNIQ_5BDB23D2B6FCFB2 ON rpg_challenge_friends');
        $this->addSql('DROP INDEX UNIQ_F6479702B6FCFB2 ON rpg_challenge_global');
        $this->addSql('DROP INDEX UNIQ_AA07E8632B6FCFB2 ON rpg_challenge_global_patient');
        $this->addSql('DROP INDEX UNIQ_F75BD9DA2B6FCFB2 ON rpg_indicator');
        $this->addSql('DROP INDEX UNIQ_2CBF373C2B6FCFB2 ON rpg_milestones');
        $this->addSql('DROP INDEX UNIQ_FBCA13082B6FCFB2 ON rpg_rewards');
        $this->addSql('DROP INDEX UNIQ_A069EAE62B6FCFB2 ON rpg_rewards_awarded');
        $this->addSql('DROP INDEX UNIQ_7808C9532B6FCFB2 ON rpg_xp');
        $this->addSql('DROP INDEX UNIQ_D8A8D9AF2B6FCFB2 ON site_nav_item');
        $this->addSql('DROP INDEX UNIQ_264924A92B6FCFB2 ON site_news');
        $this->addSql('DROP INDEX UNIQ_99CCF5CE2B6FCFB2 ON sync_queue');
        $this->addSql('DROP INDEX UNIQ_B4645ADE2B6FCFB2 ON third_party_service');
        $this->addSql('DROP INDEX UNIQ_A1A335C22B6FCFB2 ON tracking_device');
        $this->addSql('DROP INDEX UNIQ_DC68A6042B6FCFB2 ON unit_of_measurement');
        $this->addSql('DROP INDEX UNIQ_B40DF75D2B6FCFB2 ON uploaded_file');
        $this->addSql('DROP INDEX UNIQ_56D709E22B6FCFB2 ON workout_categories');
        $this->addSql('DROP INDEX UNIQ_F1E2B2D72B6FCFB2 ON workout_equipment');
        $this->addSql('DROP INDEX UNIQ_76AB38AA2B6FCFB2 ON workout_exercise');
        $this->addSql('DROP INDEX UNIQ_E99206212B6FCFB2 ON workout_muscle');
        $this->addSql('DROP INDEX UNIQ_56B0B0112B6FCFB2 ON workout_muscle_relation');

    }
}
