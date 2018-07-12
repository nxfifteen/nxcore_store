<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180710205753 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE heart_rate_resting DROP FOREIGN KEY FK_DE345D1019536BEF');
        $this->addSql('ALTER TABLE heart_rate_resting DROP FOREIGN KEY FK_DE345D104AA71F69');
        $this->addSql('ALTER TABLE heart_rate_resting DROP FOREIGN KEY FK_DE345D1066951C68');
        $this->addSql('ALTER TABLE heart_rate_resting DROP FOREIGN KEY FK_DE345D10F848A361');
        $this->addSql('DROP INDEX IDX_DE345D1066951C68 ON heart_rate_resting');
        $this->addSql('DROP INDEX IDX_DE345D104AA71F69 ON heart_rate_resting');
        $this->addSql('DROP INDEX IDX_DE345D1019536BEF ON heart_rate_resting');
        $this->addSql('DROP INDEX IDX_DE345D10F848A361 ON heart_rate_resting');
        $this->addSql('ALTER TABLE heart_rate_resting ADD heart_rate_id INT DEFAULT NULL, ADD unit INT DEFAULT NULL, DROP out_of_range_id, DROP fat_burn_id, DROP cardio_id, DROP peak_id, DROP out_of_range_time, DROP fat_burn_time, DROP cardio_time, DROP peak_time');
        $this->addSql('ALTER TABLE heart_rate_resting ADD CONSTRAINT FK_DE345D10882F7BA6 FOREIGN KEY (heart_rate_id) REFERENCES heart_rate (id)');
        $this->addSql('ALTER TABLE heart_rate_resting ADD CONSTRAINT FK_DE345D10DCBB0C53 FOREIGN KEY (unit) REFERENCES unit_of_measurement (id)');
        $this->addSql('CREATE INDEX IDX_DE345D10882F7BA6 ON heart_rate_resting (heart_rate_id)');
        $this->addSql('CREATE INDEX IDX_DE345D10DCBB0C53 ON heart_rate_resting (unit)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE heart_rate_resting DROP FOREIGN KEY FK_DE345D10882F7BA6');
        $this->addSql('ALTER TABLE heart_rate_resting DROP FOREIGN KEY FK_DE345D10DCBB0C53');
        $this->addSql('DROP INDEX IDX_DE345D10882F7BA6 ON heart_rate_resting');
        $this->addSql('DROP INDEX IDX_DE345D10DCBB0C53 ON heart_rate_resting');
        $this->addSql('ALTER TABLE heart_rate_resting ADD out_of_range_id INT DEFAULT NULL, ADD fat_burn_id INT DEFAULT NULL, ADD cardio_id INT DEFAULT NULL, ADD peak_id INT DEFAULT NULL, ADD out_of_range_time INT DEFAULT NULL, ADD fat_burn_time INT DEFAULT NULL, ADD cardio_time INT DEFAULT NULL, ADD peak_time INT DEFAULT NULL, DROP heart_rate_id, DROP unit');
        $this->addSql('ALTER TABLE heart_rate_resting ADD CONSTRAINT FK_DE345D1019536BEF FOREIGN KEY (cardio_id) REFERENCES heart_rate_cardio (id)');
        $this->addSql('ALTER TABLE heart_rate_resting ADD CONSTRAINT FK_DE345D104AA71F69 FOREIGN KEY (fat_burn_id) REFERENCES heart_rate_fat_burn (id)');
        $this->addSql('ALTER TABLE heart_rate_resting ADD CONSTRAINT FK_DE345D1066951C68 FOREIGN KEY (out_of_range_id) REFERENCES heart_rate_out_of_range (id)');
        $this->addSql('ALTER TABLE heart_rate_resting ADD CONSTRAINT FK_DE345D10F848A361 FOREIGN KEY (peak_id) REFERENCES heart_rate_peak (id)');
        $this->addSql('CREATE INDEX IDX_DE345D1066951C68 ON heart_rate_resting (out_of_range_id)');
        $this->addSql('CREATE INDEX IDX_DE345D104AA71F69 ON heart_rate_resting (fat_burn_id)');
        $this->addSql('CREATE INDEX IDX_DE345D1019536BEF ON heart_rate_resting (cardio_id)');
        $this->addSql('CREATE INDEX IDX_DE345D10F848A361 ON heart_rate_resting (peak_id)');
    }
}
