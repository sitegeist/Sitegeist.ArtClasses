<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Exception as DbalException;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add necessary tables
 */
class Version20230905092427 extends AbstractMigration
{
    /**
     * @throws DbalException
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql');

        $this->addSql('CREATE TABLE sitegeist_artclasses_imageinterpretation (asset_id VARCHAR(40) NOT NULL, interpretation JSON NOT NULL, full_text LONGTEXT NOT NULL, PRIMARY KEY(asset_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @throws DbalException
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql');

        $this->addSql('DROP TABLE sitegeist_artclasses_imageinterpretation');
    }
}
