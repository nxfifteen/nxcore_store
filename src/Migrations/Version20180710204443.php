<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180710204443 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE heart_rate ADD out_of_range_time INT DEFAULT NULL, ADD fat_burn_time INT DEFAULT NULL, ADD cardio_time INT DEFAULT NULL, ADD peak_time INT DEFAULT NULL');
        $this->addSql('DROP INDEX UniqueReading ON heart_rate_cardio');
        $this->addSql('ALTER TABLE heart_rate_cardio DROP time');
        $this->addSql('CREATE UNIQUE INDEX UniqueReading ON heart_rate_cardio (average, min, max)');
        $this->addSql('DROP INDEX UniqueReading ON heart_rate_fat_burn');
        $this->addSql('ALTER TABLE heart_rate_fat_burn DROP time');
        $this->addSql('CREATE UNIQUE INDEX UniqueReading ON heart_rate_fat_burn (average, min, max)');
        $this->addSql('DROP INDEX UniqueReading ON heart_rate_out_of_range');
        $this->addSql('ALTER TABLE heart_rate_out_of_range DROP time');
        $this->addSql('CREATE UNIQUE INDEX UniqueReading ON heart_rate_out_of_range (average, min, max)');
        $this->addSql('DROP INDEX UniqueReading ON heart_rate_peak');
        $this->addSql('ALTER TABLE heart_rate_peak DROP time');
        $this->addSql('CREATE UNIQUE INDEX UniqueReading ON heart_rate_peak (average, min, max)');
        $this->addSql('ALTER TABLE heart_rate_resting ADD out_of_range_time INT DEFAULT NULL, ADD fat_burn_time INT DEFAULT NULL, ADD cardio_time INT DEFAULT NULL, ADD peak_time INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE heart_rate DROP out_of_range_time, DROP fat_burn_time, DROP cardio_time, DROP peak_time');
        $this->addSql('DROP INDEX UniqueReading ON heart_rate_cardio');
        $this->addSql('ALTER TABLE heart_rate_cardio ADD time INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UniqueReading ON heart_rate_cardio (average, min, max, time)');
        $this->addSql('DROP INDEX UniqueReading ON heart_rate_fat_burn');
        $this->addSql('ALTER TABLE heart_rate_fat_burn ADD time INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UniqueReading ON heart_rate_fat_burn (average, min, max, time)');
        $this->addSql('DROP INDEX UniqueReading ON heart_rate_out_of_range');
        $this->addSql('ALTER TABLE heart_rate_out_of_range ADD time INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UniqueReading ON heart_rate_out_of_range (average, min, max, time)');
        $this->addSql('DROP INDEX UniqueReading ON heart_rate_peak');
        $this->addSql('ALTER TABLE heart_rate_peak ADD time INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UniqueReading ON heart_rate_peak (average, min, max, time)');
        $this->addSql('ALTER TABLE heart_rate_resting DROP out_of_range_time, DROP fat_burn_time, DROP cardio_time, DROP peak_time');
    }
}
