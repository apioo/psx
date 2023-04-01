<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230326195309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('app_population')) {
            $table = $schema->createTable('app_population');
            $table->addColumn('id', 'integer', ['autoincrement' => true]);
            $table->addColumn('place', 'integer');
            $table->addColumn('region', 'string');
            $table->addColumn('population', 'integer');
            $table->addColumn('users', 'integer');
            $table->addColumn('world_users', 'float');
            $table->addColumn('insert_date', 'datetime');
            $table->setPrimaryKey(['id']);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
