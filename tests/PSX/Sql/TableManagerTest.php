<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

use PDOException;
use PSX\Cache;
use PSX\Sql\Table\Reader;

/**
 * TableManagerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TableManagerTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		try
		{
			$this->sql = getContainer()->get('sql');
		}
		catch(PDOException $e)
		{
			$this->markTestSkipped($e->getMessage());
		}
	}

	public function testGetTable()
	{
		$tm = new TableManager($this->sql);

		$table = $tm->getTable('PSX\Handler\Database\TestTable');

		$this->assertInstanceOf('PSX\Sql\TableInterface', $table);
	}

	public function testGetTableCustomReader()
	{
		$tm = new TableManager($this->sql);
		$tm->setDefaultReader(new Reader\MysqlDescribe($this->sql));

		$table = $tm->getTable('psx_sql_table_test');

		$this->assertInstanceOf('PSX\Sql\TableInterface', $table);

		$columns = $table->getColumns();

		$this->assertEquals(TableInterface::TYPE_INT | 10 | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT, $columns['id']);
		$this->assertEquals(TableInterface::TYPE_VARCHAR | 32, $columns['title']);
		$this->assertEquals(TableInterface::TYPE_DATETIME, $columns['date']);
	}

	public function testGetTableCaching()
	{
		$cache = new Cache(new Cache\Handler\Memory());

		$this->assertFalse($cache->getItem('__TD__dd205a64cc84c9bab0bce59595154301')->isHit());

		$tm = new TableManager($this->sql);
		$tm->setCache($cache);
		$tm->setDefaultReader(new Reader\MysqlDescribe($this->sql));

		$table = $tm->getTable('psx_sql_table_test');

		$this->assertInstanceOf('PSX\Sql\TableInterface', $table);

		$columns = $table->getColumns();

		$this->assertEquals(TableInterface::TYPE_INT | 10 | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT, $columns['id']);
		$this->assertEquals(TableInterface::TYPE_VARCHAR | 32, $columns['title']);
		$this->assertEquals(TableInterface::TYPE_DATETIME, $columns['date']);

		// now we must have an cache entry in the handler
		$item = $cache->getItem('__TD__dd205a64cc84c9bab0bce59595154301');

		$this->assertInstanceOf('PSX\Sql\Table\Definition', $item->get());

		// this must be called from cache
		$table = $tm->getTable('psx_sql_table_test');

		$this->assertInstanceOf('PSX\Sql\TableInterface', $table);
	}
}
