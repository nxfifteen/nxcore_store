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
final class Version20180708082011 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sleep_episode (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, unit INT DEFAULT NULL, service INT DEFAULT NULL, remote_id BIGINT DEFAULT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, latency_to_sleep_onset INT DEFAULT NULL, latency_to_arising INT DEFAULT NULL, total_sleep_time INT DEFAULT NULL, number_of_awakenings INT DEFAULT NULL, is_main_sleep TINYINT(1) DEFAULT NULL, efficiency_percentage INT DEFAULT NULL, INDEX IDX_F3C63146B899279 (patient_id), INDEX IDX_F3C6314DCBB0C53 (unit), INDEX IDX_F3C6314E19D9AD2 (service), UNIQUE INDEX DateRecord (patient_id, service, start_time), UNIQUE INDEX RemoteId (patient_id, remote_id, service), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sleep_levels (id INT AUTO_INCREMENT NOT NULL, sleep_episode INT DEFAULT NULL, unit INT DEFAULT NULL, date_time DATETIME DEFAULT NULL, level VARCHAR(6) DEFAULT NULL, value INT DEFAULT NULL, INDEX IDX_4D0573FF3C6314 (sleep_episode), INDEX IDX_4D0573FDCBB0C53 (unit), UNIQUE INDEX SleepLevel (sleep_episode, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sleep_episode ADD CONSTRAINT FK_F3C63146B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE sleep_episode ADD CONSTRAINT FK_F3C6314DCBB0C53 FOREIGN KEY (unit) REFERENCES unit_of_measurement (id)');
        $this->addSql('ALTER TABLE sleep_episode ADD CONSTRAINT FK_F3C6314E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE sleep_levels ADD CONSTRAINT FK_4D0573FF3C6314 FOREIGN KEY (sleep_episode) REFERENCES sleep_episode (id)');
        $this->addSql('ALTER TABLE sleep_levels ADD CONSTRAINT FK_4D0573FDCBB0C53 FOREIGN KEY (unit) REFERENCES unit_of_measurement (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sleep_levels DROP FOREIGN KEY FK_4D0573FF3C6314');
        $this->addSql('DROP TABLE sleep_episode');
        $this->addSql('DROP TABLE sleep_levels');
    }
}
