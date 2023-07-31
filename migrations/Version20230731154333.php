<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230731154333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD solde INT DEFAULT NULL, CHANGE contact contact VARCHAR(15) NOT NULL, CHANGE code_parrain code_parrain VARCHAR(10) DEFAULT NULL, CHANGE code_parrainage code_parrainage VARCHAR(10) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP solde, CHANGE contact contact VARCHAR(20) NOT NULL, CHANGE code_parrain code_parrain VARCHAR(20) DEFAULT NULL, CHANGE code_parrainage code_parrainage VARCHAR(20) NOT NULL');
    }
}
