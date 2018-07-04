<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180704163918 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE count_daily_calories (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, goal INT DEFAULT NULL, INDEX IDX_27AF31BC6B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE count_daily_distance (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, goal INT DEFAULT NULL, INDEX IDX_1E589C3C6B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE min_daily_fairly (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, INDEX IDX_415CE5B46B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE min_daily_lightly (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, INDEX IDX_DAA1CD006B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE min_daily_sedentary (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, INDEX IDX_94B437886B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE min_daily_very (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, date_time DATE DEFAULT NULL, value INT DEFAULT NULL, goal INT DEFAULT NULL, INDEX IDX_FB8F4C6F6B899279 (patient_id), UNIQUE INDEX DateReading (patient_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE count_daily_calories ADD CONSTRAINT FK_27AF31BC6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE count_daily_distance ADD CONSTRAINT FK_1E589C3C6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE min_daily_fairly ADD CONSTRAINT FK_415CE5B46B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE min_daily_lightly ADD CONSTRAINT FK_DAA1CD006B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE min_daily_sedentary ADD CONSTRAINT FK_94B437886B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE min_daily_very ADD CONSTRAINT FK_FB8F4C6F6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE count_daily_calories');
        $this->addSql('DROP TABLE count_daily_distance');
        $this->addSql('DROP TABLE min_daily_fairly');
        $this->addSql('DROP TABLE min_daily_lightly');
        $this->addSql('DROP TABLE min_daily_sedentary');
        $this->addSql('DROP TABLE min_daily_very');
    }
}
