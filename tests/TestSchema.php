<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX;

use Doctrine\DBAL\Schema\Schema;

/**
 * TestSchema
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestSchema
{
    public static function getSchema()
    {
        $schema = new Schema();

        self::addTableCacheHandlerSqlTest($schema);
        self::addTableSessionHandlerSqlTest($schema);
        self::addTableHandlerComment($schema);
        self::addTableCommandTest($schema);
        self::addTableSqlTableTest($schema);
        self::addTableSqlProviderNews($schema);
        self::addTableSqlProviderAuthor($schema);

        return $schema;
    }

    protected static function addTableCacheHandlerSqlTest(Schema $schema)
    {
        $table = $schema->createTable('psx_cache_handler_sql_test');
        $table->addColumn('id', 'string', array('length' => 32));
        $table->addColumn('content', 'blob');
        $table->addColumn('date', 'datetime', array('notnull' => false, 'default' => null));
        $table->setPrimaryKey(array('id'));
    }

    protected static function addTableSessionHandlerSqlTest(Schema $schema)
    {
        $table = $schema->createTable('psx_session_handler_sql_test');
        $table->addColumn('id', 'string', array('length' => 32));
        $table->addColumn('content', 'blob');
        $table->addColumn('date', 'datetime');
        $table->setPrimaryKey(array('id'));
    }

    protected static function addTableHandlerComment(Schema $schema)
    {
        $table = $schema->createTable('psx_handler_comment');
        $table->addColumn('id', 'integer', array('length' => 10, 'autoincrement' => true));
        $table->addColumn('userId', 'integer', array('length' => 10));
        $table->addColumn('title', 'string', array('length' => 32));
        $table->addColumn('date', 'datetime');
        $table->setPrimaryKey(array('id'));
    }

    protected static function addTableSqlTableTest(Schema $schema)
    {
        $table = $schema->createTable('psx_sql_table_test');
        $table->addColumn('id', 'integer', array('length' => 10, 'autoincrement' => true));
        $table->addColumn('title', 'string', array('length' => 32));
        $table->addColumn('date', 'datetime');
        $table->setPrimaryKey(array('id'));
    }

    protected static function addTableCommandTest(Schema $schema)
    {
        $table = $schema->createTable('psx_table_command_test');
        $table->addColumn('id', 'integer', array('length' => 10, 'autoincrement' => true));
        $table->addColumn('col_bigint', 'bigint');
        $table->addColumn('col_blob', 'blob');
        $table->addColumn('col_boolean', 'boolean');
        $table->addColumn('col_datetime', 'datetime');
        $table->addColumn('col_datetimetz', 'datetimetz');
        $table->addColumn('col_date', 'date');
        $table->addColumn('col_decimal', 'decimal');
        $table->addColumn('col_float', 'float');
        $table->addColumn('col_integer', 'integer');
        $table->addColumn('col_smallint', 'smallint');
        $table->addColumn('col_text', 'text');
        $table->addColumn('col_time', 'time');
        $table->addColumn('col_string', 'string');
        $table->addColumn('col_array', 'text', array('notnull' => false));
        $table->addColumn('col_object', 'text', array('notnull' => false));
        $table->setPrimaryKey(array('id'));
    }

    protected static function addTableSqlProviderNews(Schema $schema)
    {
        $table = $schema->createTable('psx_sql_provider_news');
        $table->addColumn('id', 'integer', array('length' => 10, 'autoincrement' => true));
        $table->addColumn('authorId', 'integer', array('length' => 10));
        $table->addColumn('title', 'string', array('length' => 32));
        $table->addColumn('createDate', 'datetime');
        $table->setPrimaryKey(array('id'));
    }

    protected static function addTableSqlProviderAuthor(Schema $schema)
    {
        $table = $schema->createTable('psx_sql_provider_author');
        $table->addColumn('id', 'string', array('length' => 32));
        $table->addColumn('name', 'string');
        $table->addColumn('uri', 'string');
        $table->setPrimaryKey(array('id'));
    }
}
