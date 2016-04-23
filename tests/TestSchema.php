<?php

namespace PSX\Project\Tests;

use Doctrine\DBAL\Schema\Schema;

class TestSchema
{
    public static function getSchema()
    {
        $schema = new Schema();

        $table = $schema->createTable('population');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('place', 'integer');
        $table->addColumn('region', 'string');
        $table->addColumn('population', 'integer');
        $table->addColumn('users', 'integer');
        $table->addColumn('worldUsers', 'float');
        $table->addColumn('datetime', 'datetime');
        $table->setPrimaryKey(array('id'));

        return $schema;
    }
}
