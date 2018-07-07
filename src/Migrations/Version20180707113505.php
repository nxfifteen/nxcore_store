<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180707113505 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tracking_device ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tracking_device ADD CONSTRAINT FK_A1A335C2E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_A1A335C2E19D9AD2 ON tracking_device (service)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tracking_device DROP FOREIGN KEY FK_A1A335C2E19D9AD2');
        $this->addSql('DROP INDEX IDX_A1A335C2E19D9AD2 ON tracking_device');
        $this->addSql('ALTER TABLE tracking_device DROP service');
    }
}
