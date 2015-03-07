<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

