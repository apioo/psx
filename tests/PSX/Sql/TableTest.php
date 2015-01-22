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

use PSX\Config;
use PSX\Data\Record;
use PSX\DateTime;
use PSX\Sql\TableInterface;
use PSX\Test\TableDataSet;

/**
 * TableTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TableTest extends DbTestCase
{
	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(__DIR__ . '/table_fixture.xml');
	}

	public function testTable()
	{
		$table = new Table($this->connection, 'foo_table', array('bar' => TableInterface::TYPE_INT), array());

		$this->assertEquals('foo_table', $table->getName());
		$this->assertEquals(array('bar' => TableInterface::TYPE_INT), $table->getColumns());
		$this->assertEquals(array(), $table->getConnections());

		$table->addConnection('bar', 'bar_table');

		$this->assertEquals(array('bar' => 'bar_table'), $table->getConnections());
	}

	/**
	 * @expectedException PSX\Exception
	 */
	public function testAddConnectionInvalidColumn()
	{
		$table = new Table($this->connection, 'foo_table', array());
		$table->addConnection('bar', 'bar_table');
	}
}

