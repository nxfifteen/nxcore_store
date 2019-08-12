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
final class Version20180707204627 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rewards_earned (id INT AUTO_INCREMENT NOT NULL, reward INT DEFAULT NULL, patient_id INT DEFAULT NULL, part_of_day INT DEFAULT NULL, date DATE DEFAULT NULL, INDEX IDX_21DEB84B4ED17253 (reward), INDEX IDX_21DEB84B6B899279 (patient_id), INDEX IDX_21DEB84BEE0DB122 (part_of_day), UNIQUE INDEX Rewarded (date, reward, patient_id, part_of_day), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rewards_earned ADD CONSTRAINT FK_21DEB84B4ED17253 FOREIGN KEY (reward) REFERENCES reward (id)');
        $this->addSql('ALTER TABLE rewards_earned ADD CONSTRAINT FK_21DEB84B6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE rewards_earned ADD CONSTRAINT FK_21DEB84BEE0DB122 FOREIGN KEY (part_of_day) REFERENCES part_of_day (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE rewards_earned');
    }
}
