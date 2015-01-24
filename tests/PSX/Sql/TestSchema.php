<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Sql;

use Doctrine\DBAL\Schema\Schema;

/**
 * TestSchema
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
		self::addTableSqlTableTest($schema);
		self::addTableCommandTest($schema);

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
		$table->addColumn('col_array', 'array');
		$table->addColumn('col_object', 'object');
		$table->setPrimaryKey(array('id'));
	}
}
