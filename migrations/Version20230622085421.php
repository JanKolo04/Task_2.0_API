<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230622085421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan MODIFY planId INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON plan');
        $this->addSql('ALTER TABLE plan CHANGE planId plan_id INT AUTO_INCREMENT NOT NULL, CHANGE bgColor bg_color VARCHAR(7) NOT NULL');
        $this->addSql('ALTER TABLE plan ADD PRIMARY KEY (plan_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan MODIFY plan_id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON plan');
        $this->addSql('ALTER TABLE plan CHANGE plan_id planId INT AUTO_INCREMENT NOT NULL, CHANGE bg_color bgColor VARCHAR(7) NOT NULL');
        $this->addSql('ALTER TABLE plan ADD PRIMARY KEY (planId)');
    }
}
