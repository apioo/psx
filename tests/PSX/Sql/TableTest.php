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

use PSX\Config;
use PSX\Data\Record;
use PSX\DateTime;
use PSX\Sql;
use PSX\Sql\Table\Select;
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
	protected $table = 'psx_sql_table_test';

	public function getDataSet()
	{
		$dataSet = new TableDataSet();
		$dataSet->addTable(new TableTestTable($this->connection), array(
			array('id' => null, 'title' => 'foo', 'date' => date(DateTime::SQL)),
			array('id' => null, 'title' => 'bar', 'date' => date(DateTime::SQL)),
			array('id' => null, 'title' => 'test', 'date' => date(DateTime::SQL)),
			array('id' => null, 'title' => 'fooooo', 'date' => date(DateTime::SQL)),
		));

		return $dataSet;
	}

	public function testGetDisplayName()
	{
		$table = new TableTestTable($this->connection);

		$this->assertEquals('test', $table->getDisplayName());
	}

	public function testGetPrimaryKey()
	{
		$table = new TableTestTable($this->connection);

		$this->assertEquals('id', $table->getPrimaryKey());
	}

	public function testGetFirstColumnWithAttr()
	{
		$table = new TableTestTable($this->connection);

		$this->assertEquals('id', $table->getFirstColumnWithAttr(TableTestTable::PRIMARY_KEY));
		$this->assertEquals('id', $table->getFirstColumnWithAttr(TableTestTable::AUTO_INCREMENT));
	}

	public function testGetFirstColumnWithType()
	{
		$table = new TableTestTable($this->connection);

		$this->assertEquals('id', $table->getFirstColumnWithType(TableTestTable::TYPE_INT));
		$this->assertEquals('title', $table->getFirstColumnWithType(TableTestTable::TYPE_VARCHAR));
		$this->assertEquals('date', $table->getFirstColumnWithType(TableTestTable::TYPE_DATETIME));
	}

	public function testGetValidColumns()
	{
		$table = new TableTestTable($this->connection);

		$actual = $table->getValidColumns(array('test', 'date', 'id', 'foo', 'bar', 'title', 'blub'));
		$expect = array(1 => 'date', 2 => 'id', 5 => 'title');

		$this->assertEquals($expect, $actual);
	}

	public function testSelect()
	{
		$table  = new TableTestTable($this->connection);
		$select = $table->select(array('id', 'title'));

		$record = $select->where('id', '=', 2)->getOneBy(new Condition());

		$this->assertEquals(array('id' => 2, 'title' => 'bar'), $record->getRecordInfo()->getData());
	}

	public function testGetLastSelect()
	{
		$table  = new TableTestTable($this->connection);
		$select = $table->select(array('id', 'title'));

		$this->assertEquals(true, $table->getLastSelect() instanceof Select);
	}
}

class TableTestTable extends TableAbstract
{
	public function getConnections()
	{
		return array();
	}

	public function getName()
	{
		return 'psx_sql_table_test';
	}

	public function getColumns()
	{
		return array(
			'id'    => self::TYPE_INT | 10 | self::PRIMARY_KEY | self::AUTO_INCREMENT,
			'title' => self::TYPE_VARCHAR | 32,
			'date'  => self::TYPE_DATETIME,
		);
	}
}

class TableTestRecord
{
	public function __construct()
	{
	}
}

