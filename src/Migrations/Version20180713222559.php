<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180713222559 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE body_fat ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE body_fat ADD CONSTRAINT FK_190FCCF4E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_190FCCF4E19D9AD2 ON body_fat (service)');
        $this->addSql('ALTER TABLE body_weight ADD service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE body_weight ADD CONSTRAINT FK_92D1F647E19D9AD2 FOREIGN KEY (service) REFERENCES third_party_service (id)');
        $this->addSql('CREATE INDEX IDX_92D1F647E19D9AD2 ON body_weight (service)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE body_bmi DROP service');
        $this->addSql('ALTER TABLE body_fat DROP FOREIGN KEY FK_190FCCF4E19D9AD2');
        $this->addSql('DROP INDEX IDX_190FCCF4E19D9AD2 ON body_fat');
        $this->addSql('ALTER TABLE body_fat DROP service');
        $this->addSql('ALTER TABLE body_weight DROP FOREIGN KEY FK_92D1F647E19D9AD2');
        $this->addSql('DROP INDEX IDX_92D1F647E19D9AD2 ON body_weight');
        $this->addSql('ALTER TABLE body_weight DROP service');
    }
}
