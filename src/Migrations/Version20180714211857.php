<?php

/*
* This file is part of the Storage module in NxFIFTEEN Core.
*
* Copyright (c) 2019. Stuart McCulloch Anderson
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*
* @package     Store
* @version     0.0.0.x
* @since       0.0.0.1
* @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
* @link        https://nxfifteen.me.uk NxFIFTEEN
* @link        https://git.nxfifteen.rocks/nx-health NxFIFTEEN Core
* @link        https://git.nxfifteen.rocks/nx-health/store NxFIFTEEN Core Storage
* @copyright   2019 Stuart McCulloch Anderson
* @license     https://license.nxfifteen.rocks/mit/2015-2019/ MIT
*/

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180714211857 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE count_daily_calories ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE count_daily_calories ADD CONSTRAINT FK_27AF31BCE19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_27AF31BCE19D9AD2 ON count_daily_calories (service)');
        $this->addSql('ALTER TABLE count_daily_distance ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE count_daily_distance ADD CONSTRAINT FK_1E589C3CE19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_1E589C3CE19D9AD2 ON count_daily_distance (service)');
        $this->addSql('ALTER TABLE count_daily_elevation ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE count_daily_elevation ADD CONSTRAINT FK_89F84FB6E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_89F84FB6E19D9AD2 ON count_daily_elevation (service)');
        $this->addSql('ALTER TABLE count_daily_floor ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE count_daily_floor ADD CONSTRAINT FK_4DE77C7DE19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_4DE77C7DE19D9AD2 ON count_daily_floor (service)');
        $this->addSql('ALTER TABLE count_daily_step ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE count_daily_step ADD CONSTRAINT FK_5862E52EE19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_5862E52EE19D9AD2 ON count_daily_step (service)');
        $this->addSql('ALTER TABLE heart_rate ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE heart_rate ADD CONSTRAINT FK_84A07ED6E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_84A07ED6E19D9AD2 ON heart_rate (service)');
        $this->addSql('ALTER TABLE heart_rate_resting ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE heart_rate_resting ADD CONSTRAINT FK_DE345D10E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_DE345D10E19D9AD2 ON heart_rate_resting (service)');
        $this->addSql('ALTER TABLE intraday_step ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intraday_step ADD CONSTRAINT FK_3DDD0432E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_3DDD0432E19D9AD2 ON intraday_step (service)');
        $this->addSql('ALTER TABLE min_daily_fairly ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE min_daily_fairly ADD CONSTRAINT FK_415CE5B4E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_415CE5B4E19D9AD2 ON min_daily_fairly (service)');
        $this->addSql('ALTER TABLE min_daily_lightly ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE min_daily_lightly ADD CONSTRAINT FK_DAA1CD00E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_DAA1CD00E19D9AD2 ON min_daily_lightly (service)');
        $this->addSql('ALTER TABLE min_daily_sedentary ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE min_daily_sedentary ADD CONSTRAINT FK_94B43788E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_94B43788E19D9AD2 ON min_daily_sedentary (service)');
        $this->addSql('ALTER TABLE min_daily_very ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE min_daily_very ADD CONSTRAINT FK_FB8F4C6FE19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_FB8F4C6FE19D9AD2 ON min_daily_very (service)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE count_daily_calories DROP FOREIGN KEY FK_27AF31BCE19D9AD2');
        $this->addSql('DROP INDEX IDX_27AF31BCE19D9AD2 ON count_daily_calories');
        $this->addSql('ALTER TABLE count_daily_calories DROP service');
        $this->addSql('ALTER TABLE count_daily_distance DROP FOREIGN KEY FK_1E589C3CE19D9AD2');
        $this->addSql('DROP INDEX IDX_1E589C3CE19D9AD2 ON count_daily_distance');
        $this->addSql('ALTER TABLE count_daily_distance DROP service');
        $this->addSql('ALTER TABLE count_daily_elevation DROP FOREIGN KEY FK_89F84FB6E19D9AD2');
        $this->addSql('DROP INDEX IDX_89F84FB6E19D9AD2 ON count_daily_elevation');
        $this->addSql('ALTER TABLE count_daily_elevation DROP service');
        $this->addSql('ALTER TABLE count_daily_floor DROP FOREIGN KEY FK_4DE77C7DE19D9AD2');
        $this->addSql('DROP INDEX IDX_4DE77C7DE19D9AD2 ON count_daily_floor');
        $this->addSql('ALTER TABLE count_daily_floor DROP service');
        $this->addSql('ALTER TABLE count_daily_step DROP FOREIGN KEY FK_5862E52EE19D9AD2');
        $this->addSql('DROP INDEX IDX_5862E52EE19D9AD2 ON count_daily_step');
        $this->addSql('ALTER TABLE count_daily_step DROP service');
        $this->addSql('ALTER TABLE heart_rate DROP FOREIGN KEY FK_84A07ED6E19D9AD2');
        $this->addSql('DROP INDEX IDX_84A07ED6E19D9AD2 ON heart_rate');
        $this->addSql('ALTER TABLE heart_rate DROP service');
        $this->addSql('ALTER TABLE heart_rate_resting DROP FOREIGN KEY FK_DE345D10E19D9AD2');
        $this->addSql('DROP INDEX IDX_DE345D10E19D9AD2 ON heart_rate_resting');
        $this->addSql('ALTER TABLE heart_rate_resting DROP service');
        $this->addSql('ALTER TABLE intraday_step DROP FOREIGN KEY FK_3DDD0432E19D9AD2');
        $this->addSql('DROP INDEX IDX_3DDD0432E19D9AD2 ON intraday_step');
        $this->addSql('ALTER TABLE intraday_step DROP service');
        $this->addSql('ALTER TABLE min_daily_fairly DROP FOREIGN KEY FK_415CE5B4E19D9AD2');
        $this->addSql('DROP INDEX IDX_415CE5B4E19D9AD2 ON min_daily_fairly');
        $this->addSql('ALTER TABLE min_daily_fairly DROP service');
        $this->addSql('ALTER TABLE min_daily_lightly DROP FOREIGN KEY FK_DAA1CD00E19D9AD2');
        $this->addSql('DROP INDEX IDX_DAA1CD00E19D9AD2 ON min_daily_lightly');
        $this->addSql('ALTER TABLE min_daily_lightly DROP service');
        $this->addSql('ALTER TABLE min_daily_sedentary DROP FOREIGN KEY FK_94B43788E19D9AD2');
        $this->addSql('DROP INDEX IDX_94B43788E19D9AD2 ON min_daily_sedentary');
        $this->addSql('ALTER TABLE min_daily_sedentary DROP service');
        $this->addSql('ALTER TABLE min_daily_very DROP FOREIGN KEY FK_FB8F4C6FE19D9AD2');
        $this->addSql('DROP INDEX IDX_FB8F4C6FE19D9AD2 ON min_daily_very');
        $this->addSql('ALTER TABLE min_daily_very DROP service');
    }
}
