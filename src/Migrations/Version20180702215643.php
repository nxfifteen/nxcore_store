<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180702215643 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE body_fat DROP descriptive_statistic, CHANGE part_of_day part_of_day enum(\'morning\', \'afternoon\', \'evening\', \'night\')');
        $this->addSql('ALTER TABLE body_weight DROP descriptive_statistic, CHANGE unit unit enum(\'fg\', \'pg\', \'ng\', \'ug\', \'mg\', \'g\', \'kg\', \'Metric Ton\', \'gr\', \'oz\', \'lb\', \'Ton\'), CHANGE part_of_day part_of_day enum(\'morning\', \'afternoon\', \'evening\', \'night\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE body_fat ADD descriptive_statistic VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE part_of_day part_of_day VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE body_weight ADD descriptive_statistic VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE unit unit VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE part_of_day part_of_day VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
