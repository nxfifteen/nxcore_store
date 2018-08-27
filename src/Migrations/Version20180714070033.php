<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180714070033 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE api_access_log (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, service INT DEFAULT NULL, entity VARCHAR(30) DEFAULT NULL, last_retrieved DATETIME DEFAULT NULL, INDEX IDX_28D549016B899279 (patient_id), INDEX IDX_28D54901E19D9AD2 (service), UNIQUE INDEX AccessLog (patient_id, service, entity), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_access_log ADD CONSTRAINT FK_28D549016B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE api_access_log ADD CONSTRAINT FK_28D54901E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE api_access_log');
    }
}
