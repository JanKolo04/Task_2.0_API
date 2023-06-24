<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230622100100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan CHANGE plan_id plan_id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE user_in_plan DROP FOREIGN KEY user_in_plan_ibfk_1');
        $this->addSql('ALTER TABLE user_in_plan DROP FOREIGN KEY user_in_plan_ibfk_2');
        $this->addSql('DROP INDEX plan_id ON user_in_plan');
        $this->addSql('DROP INDEX user_id ON user_in_plan');
        $this->addSql('DROP INDEX IDX_AFEC7A13E899029B ON user_in_plan');
        $this->addSql('ALTER TABLE user_in_plan CHANGE plan_id plan_id INT NOT NULL, CHANGE user_id user_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan CHANGE plan_id plan_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_in_plan CHANGE plan_id plan_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_in_plan ADD CONSTRAINT user_in_plan_ibfk_1 FOREIGN KEY (user_id) REFERENCES user (user_id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_in_plan ADD CONSTRAINT user_in_plan_ibfk_2 FOREIGN KEY (plan_id) REFERENCES plan (plan_id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX plan_id ON user_in_plan (plan_id, user_id)');
        $this->addSql('CREATE INDEX user_id ON user_in_plan (user_id)');
        $this->addSql('CREATE INDEX IDX_AFEC7A13E899029B ON user_in_plan (plan_id)');
    }
}
