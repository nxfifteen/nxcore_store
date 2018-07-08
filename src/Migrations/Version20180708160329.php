<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180708160329 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE personal_plan (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, service INT DEFAULT NULL, goals VARCHAR(50) DEFAULT NULL, intensity VARCHAR(50) DEFAULT NULL, estimated_date DATE DEFAULT NULL, INDEX IDX_EC505C406B899279 (patient_id), INDEX IDX_EC505C40E19D9AD2 (service), UNIQUE INDEX PersonalGoal (goals, patient_id, service), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE personal_plan ADD CONSTRAINT FK_EC505C406B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE personal_plan ADD CONSTRAINT FK_EC505C40E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE personal_plan');
    }
}
