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
final class Version20180708164456 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE life_tracker (id INT AUTO_INCREMENT NOT NULL, service INT DEFAULT NULL, patient_id INT DEFAULT NULL, config INT DEFAULT NULL, score INT DEFAULT NULL, remote_id VARCHAR(150) DEFAULT NULL, name VARCHAR(150) DEFAULT NULL, icon VARCHAR(150) DEFAULT NULL, colour VARCHAR(6) DEFAULT NULL, INDEX IDX_A10EB3CE19D9AD2 (service), INDEX IDX_A10EB3C6B899279 (patient_id), INDEX IDX_A10EB3CD48A2F7C (config), INDEX IDX_A10EB3C32993751 (score), UNIQUE INDEX LifeIndex (service, patient_id, remote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE life_tracker_config (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) DEFAULT NULL, uom VARCHAR(255) DEFAULT NULL, min INT DEFAULT NULL, max INT DEFAULT NULL, math VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE life_tracker_score (id INT AUTO_INCREMENT NOT NULL, cond VARCHAR(255) DEFAULT NULL, compare INT DEFAULT NULL, charge INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE life_tracker ADD CONSTRAINT FK_A10EB3CE19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('ALTER TABLE life_tracker ADD CONSTRAINT FK_A10EB3C6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE life_tracker ADD CONSTRAINT FK_A10EB3CD48A2F7C FOREIGN KEY (config) REFERENCES life_tracker_config (id)');
        $this->addSql('ALTER TABLE life_tracker ADD CONSTRAINT FK_A10EB3C32993751 FOREIGN KEY (score) REFERENCES life_tracker_score (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE life_tracker DROP FOREIGN KEY FK_A10EB3CD48A2F7C');
        $this->addSql('ALTER TABLE life_tracker DROP FOREIGN KEY FK_A10EB3C32993751');
        $this->addSql('DROP TABLE life_tracker');
        $this->addSql('DROP TABLE life_tracker_config');
        $this->addSql('DROP TABLE life_tracker_score');
    }
}
