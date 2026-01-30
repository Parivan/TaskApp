<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260130084809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_2FB3D0EE5E237E06 ON project');
        $this->addSql(
            'ALTER TABLE project
                    ADD description LONGTEXT DEFAULT NULL,
                    CHANGE name title VARCHAR(255) NOT NULL'
        );

        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE2B36786B ON project (title)');
        $this->addSql('ALTER TABLE task ADD description LONGTEXT DEFAULT NULL, ADD due_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE team ADD description LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_2FB3D0EE2B36786B ON project');
        $this->addSql('ALTER TABLE project DROP description, CHANGE title name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE5E237E06 ON project (name)');
        $this->addSql('ALTER TABLE task DROP description, DROP due_date');
        $this->addSql('ALTER TABLE team DROP description');
    }
}
