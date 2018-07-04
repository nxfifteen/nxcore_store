<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180704203101 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE nutrition_information (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, unit INT DEFAULT NULL, date_time DATE NOT NULL, period VARCHAR(4) NOT NULL, amount INT DEFAULT NULL, brand VARCHAR(50) DEFAULT NULL, meal VARCHAR(50) DEFAULT NULL, calories INT DEFAULT NULL, carbs INT DEFAULT NULL, fat INT DEFAULT NULL, fiber INT DEFAULT NULL, protein INT DEFAULT NULL, sodium INT DEFAULT NULL, INDEX IDX_47B586C86B899279 (patient_id), INDEX IDX_47B586C8DCBB0C53 (unit), UNIQUE INDEX DateReading (patient_id, date_time, period, meal), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nutrition_information ADD CONSTRAINT FK_47B586C86B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE nutrition_information ADD CONSTRAINT FK_47B586C8DCBB0C53 FOREIGN KEY (unit) REFERENCES unit_of_measurement (id)');

        $this->addSql("INSERT INTO unit_of_measurement (name) VALUES ('serving')");
        $this->addSql("INSERT INTO unit_of_measurement (name) VALUES ('meal')");
        $this->addSql("INSERT INTO unit_of_measurement (name) VALUES ('day')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE nutrition_information');
    }
}
