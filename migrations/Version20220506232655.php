<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220506232655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174455B7E546 FOREIGN KEY (invoice_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9065174455B7E546 ON invoice (invoice_user_id)');
        $this->addSql('ALTER TABLE product DROP slug');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174455B7E546');
        $this->addSql('DROP INDEX IDX_9065174455B7E546 ON invoice');
        $this->addSql('ALTER TABLE product ADD slug VARCHAR(255) NOT NULL');
    }
}
