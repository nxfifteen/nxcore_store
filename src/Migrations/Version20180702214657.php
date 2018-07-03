<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180702214657 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE body_weight (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, measurement DOUBLE PRECISION NOT NULL, unit enum(\'fg\', \'pg\', \'ng\', \'ug\', \'mg\', \'g\', \'kg\', \'Metric Ton\', \'gr\', \'oz\', \'lb\', \'Ton\'), date_time DATETIME NOT NULL, part_of_day enum(\'morning\', \'afternoon\', \'evening\', \'night\'), descriptive_statistic enum(\'average\', \'maximum\', \'minimum\', \'standard deviation\', \'variance\', \'sum\', \'median\'), INDEX IDX_92D1F6476B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, fname VARCHAR(255) DEFAULT NULL, lname VARCHAR(255) DEFAULT NULL, birthday DATETIME DEFAULT NULL, height INT DEFAULT NULL, email VARCHAR(255) NOT NULL, gender VARCHAR(6) DEFAULT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE body_weight ADD CONSTRAINT FK_92D1F6476B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE body_weight DROP FOREIGN KEY FK_92D1F6476B899279');
        $this->addSql('DROP TABLE body_weight');
        $this->addSql('DROP TABLE patient');
    }
}
