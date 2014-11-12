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

use PSX\Cache;
use PSX\Sql\Table\Reader;
use PSX\Sql\TableInterface;

/**
 * TableManagerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TableManagerTest extends DbTestCase
{
	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(__DIR__ . '/table_fixture.xml');
	}

	public function testGetTable()
	{
		$reader = new Reader\MysqlDescribe($this->connection);
		$cache  = new Cache(new Cache\Handler\Memory());

		$manager = new TableManager($this->connection);
		$manager->setDefaultReader($reader);
		$manager->setCache($cache);

		$table = $manager->getTable('psx_handler_comment');

		$this->assertEquals('psx_handler_comment', $table->getName());

		$columns = $table->getColumns();

		$this->assertEquals(TableInterface::TYPE_INT | 10 | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT, $columns['id']);
		$this->assertEquals(TableInterface::TYPE_VARCHAR | 32, $columns['title']);
		$this->assertEquals(TableInterface::TYPE_DATETIME, $columns['date']);

		// next call should go through the cache
		$table = $manager->getTable('psx_handler_comment');

		$this->assertEquals('psx_handler_comment', $table->getName());

		$columns = $table->getColumns();

		$this->assertEquals(TableInterface::TYPE_INT | 10 | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT, $columns['id']);
		$this->assertEquals(TableInterface::TYPE_VARCHAR | 32, $columns['title']);
		$this->assertEquals(TableInterface::TYPE_DATETIME, $columns['date']);
	}
}
