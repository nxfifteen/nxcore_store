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
final class Version20180710211005 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE heart_rate ADD unit INT DEFAULT NULL');
        $this->addSql('ALTER TABLE heart_rate ADD CONSTRAINT FK_84A07ED6DCBB0C53 FOREIGN KEY (unit) REFERENCES unit_of_measurement (id)');
        $this->addSql('CREATE INDEX IDX_84A07ED6DCBB0C53 ON heart_rate (unit)');
        $this->addSql('ALTER TABLE heart_rate_resting DROP FOREIGN KEY FK_DE345D10DCBB0C53');
        $this->addSql('DROP INDEX IDX_DE345D10DCBB0C53 ON heart_rate_resting');
        $this->addSql('ALTER TABLE heart_rate_resting DROP unit');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE heart_rate DROP FOREIGN KEY FK_84A07ED6DCBB0C53');
        $this->addSql('DROP INDEX IDX_84A07ED6DCBB0C53 ON heart_rate');
        $this->addSql('ALTER TABLE heart_rate DROP unit');
        $this->addSql('ALTER TABLE heart_rate_resting ADD unit INT DEFAULT NULL');
        $this->addSql('ALTER TABLE heart_rate_resting ADD CONSTRAINT FK_DE345D10DCBB0C53 FOREIGN KEY (unit) REFERENCES unit_of_measurement (id)');
        $this->addSql('CREATE INDEX IDX_DE345D10DCBB0C53 ON heart_rate_resting (unit)');
    }
}
