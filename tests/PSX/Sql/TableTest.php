<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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
		$dataSet->addTable(new TableTestTable($this->sql), array(
			array('id' => null, 'title' => 'foo', 'date' => date(DateTime::SQL)),
			array('id' => null, 'title' => 'bar', 'date' => date(DateTime::SQL)),
			array('id' => null, 'title' => 'test', 'date' => date(DateTime::SQL)),
			array('id' => null, 'title' => 'fooooo', 'date' => date(DateTime::SQL)),
		));

		return $dataSet;
	}

	public function testGetDisplayName()
	{
		$table = new TableTestTable($this->sql);

		$this->assertEquals('test', $table->getDisplayName());
	}

	public function testGetPrimaryKey()
	{
		$table = new TableTestTable($this->sql);

		$this->assertEquals('id', $table->getPrimaryKey());
	}

	public function testGetFirstColumnWithAttr()
	{
		$table = new TableTestTable($this->sql);

		$this->assertEquals('id', $table->getFirstColumnWithAttr(TableTestTable::PRIMARY_KEY));
		$this->assertEquals('id', $table->getFirstColumnWithAttr(TableTestTable::AUTO_INCREMENT));
	}

	public function testGetFirstColumnWithType()
	{
		$table = new TableTestTable($this->sql);

		$this->assertEquals('id', $table->getFirstColumnWithType(TableTestTable::TYPE_INT));
		$this->assertEquals('title', $table->getFirstColumnWithType(TableTestTable::TYPE_VARCHAR));
		$this->assertEquals('date', $table->getFirstColumnWithType(TableTestTable::TYPE_DATETIME));
	}

	public function testGetValidColumns()
	{
		$table = new TableTestTable($this->sql);

		$actual = $table->getValidColumns(array('test', 'date', 'id', 'foo', 'bar', 'title', 'blub'));
		$expect = array(1 => 'date', 2 => 'id', 5 => 'title');

		$this->assertEquals($expect, $actual);
	}

	public function testSelect()
	{
		$table  = new TableTestTable($this->sql);
		$select = $table->select(array('id', 'title'));

		$this->assertEquals(array('id' => 2, 'title' => 'bar'), $select->where('id', '=', 2)->getRow());
	}

	public function testGetLastSelect()
	{
		$table  = new TableTestTable($this->sql);
		$select = $table->select(array('id', 'title'));

		$this->assertEquals(true, $table->getLastSelect() instanceof Select);
	}

	public function testGetRecord()
	{
		// update
		$table  = new TableTestTable($this->sql);
		$record = $table->getRecord(1, '\PSX\Sql\TableTestRecord');

		$this->assertEquals(true, $record instanceof TableTestRecord);
		$this->assertEquals(1, $record->id);
		$this->assertEquals('foo', $record->title);

		// create
		$table  = new TableTestTable($this->sql);
		$record = $table->getRecord(null, '\PSX\Sql\TableTestRecord');

		$this->assertEquals(true, $record instanceof TableTestRecord);
	}

	public function testGetAll()
	{
		$table = new TableTestTable($this->sql);

		$actual = $table->getAll(array('title'), new Condition(array('id', '=', 2)));
		$expect = array(array('title' => 'bar'));

		$this->assertEquals($expect, $actual);


		$actual = $table->getAll(array('title'), new Condition(array('title', 'LIKE', 'fo%')), 'id', Sql::SORT_DESC);
		$expect = array(array('title' => 'fooooo'), array('title' => 'foo'));

		$this->assertEquals($expect, $actual);


		$actual = $table->getAll(array('title'), new Condition(array('title', 'LIKE', 'fo%')), 'id', Sql::SORT_ASC, 0, 1);
		$expect = array(array('title' => 'foo'));

		$this->assertEquals($expect, $actual);
	}

	public function testGetRow()
	{
		$table = new TableTestTable($this->sql);

		$actual = $table->getRow(array('title'), new Condition(array('id', '=', 2)));
		$expect = array('title' => 'bar');

		$this->assertEquals($expect, $actual);
	}

	public function testGetCol()
	{
		$table = new TableTestTable($this->sql);

		$actual = $table->getCol('title', new Condition(array('title', 'LIKE', 'fo%')), 'id', Sql::SORT_DESC);
		$expect = array('fooooo', 'foo');

		$this->assertEquals($expect, $actual);
	}

	public function testGetField()
	{
		$table = new TableTestTable($this->sql);

		$actual = $table->getField('title', new Condition(array('id', '=', 2)));
		$expect = 'bar';

		$this->assertEquals($expect, $actual);
	}

	public function testCount()
	{
		$table = new TableTestTable($this->sql);

		$this->assertEquals(4, $table->count());
		$this->assertEquals(2, $table->count(new Condition(array('title', 'LIKE', 'fo%'))));
	}

	public function testInsert()
	{
		$value = 'foobar_' . rand(0, 999);
		$table = new TableTestTable($this->sql);

		$table->insert(array(
			'title' => $value,
			'date'  => date(DateTime::SQL)
		));

		$actual = $table->getField('title', new Condition(array('title', '=', $value)));

		$this->assertEquals($value, $actual);
	}

	public function testInsertRecord()
	{
		$value  = 'foobar_' . rand(0, 999);
		$table  = new TableTestTable($this->sql);
		$record = new Record('test', array(
			'title' => $value,
			'date'  => date(DateTime::SQL)
		));

		$table->insert($record);

		$actual = $table->getField('title', new Condition(array('title', '=', $value)));

		$this->assertEquals($value, $actual);
	}

	public function testUpdate()
	{
		$value = 'foobar_' . rand(0, 999);
		$table = new TableTestTable($this->sql);
		$con   = new Condition(array('id', '=', 3));
		$resp  = $table->getField('title', $con);

		$table->update(array(
			'title' => $value,
			'date'  => date(DateTime::SQL)
		), $con);

		$this->assertEquals(false, $resp === $table->getField('title', $con));
		$this->assertEquals(true, $value === $table->getField('title', $con));
	}

	public function testUpdateWithoutCondition()
	{
		$value = 'foobar_' . rand(0, 999);
		$table = new TableTestTable($this->sql);
		$con   = new Condition(array('id', '=', 3));
		$resp  = $table->getField('title', $con);

		$table->update(array(
			'id'    => 3,
			'title' => $value,
			'date'  => date(DateTime::SQL)
		));

		$this->assertEquals(false, $resp === $table->getField('title', $con));
		$this->assertEquals(true, $value === $table->getField('title', $con));
	}

	public function testUpdateRecord()
	{
		$value  = 'foobar_' . rand(0, 999);
		$table  = new TableTestTable($this->sql);
		$con    = new Condition(array('id', '=', 3));
		$resp   = $table->getField('title', $con);

		$record = new Record('test', array(
			'title' => $value,
			'date'  => date(DateTime::SQL)
		));

		$table->update($record, $con);

		$this->assertEquals(false, $resp === $table->getField('title', $con));
		$this->assertEquals(true, $value === $table->getField('title', $con));
	}

	public function testUpdateRecordWithoutCondition()
	{
		$value  = 'foobar_' . rand(0, 999);
		$table  = new TableTestTable($this->sql);
		$con    = new Condition(array('id', '=', 3));
		$resp   = $table->getField('title', $con);

		$record = new Record('test', array(
			'id'    => 3,
			'title' => $value,
			'date'  => date(DateTime::SQL)
		));

		$table->update($record);

		$this->assertEquals(false, $resp === $table->getField('title', $con));
		$this->assertEquals(true, $value === $table->getField('title', $con));
	}

	public function testReplace()
	{
		$value = 'foobar_' . rand(0, 999);
		$table = new TableTestTable($this->sql);
		$con   = new Condition(array('id', '=', 3));
		$resp  = $table->getField('title', $con);

		$table->replace(array(
			'id'    => 3,
			'title' => $value,
			'date'  => date(DateTime::SQL)
		));

		$this->assertEquals(false, $resp === $table->getField('title', $con));
		$this->assertEquals(true, $value === $table->getField('title', $con));
	}

	public function testDelete()
	{
		$table = new TableTestTable($this->sql);
		$con   = new Condition(array('id', '=', 3));

		$table->delete($con);

		$this->assertEquals(false, $table->getField('title', $con));
	}

	public function testDeleteWithoutCondition()
	{
		$table = new TableTestTable($this->sql);
		$con   = new Condition(array('id', '=', 3));

		$table->delete(array(
			'id' => 3
		));

		$this->assertEquals(false, $table->getField('title', $con));
	}

	public function testDeleteRecord()
	{
		$table  = new TableTestTable($this->sql);
		$con    = new Condition(array('id', '=', 3));
		$record = new Record('test', array(
			'id' => 3,
		));

		$table->delete($record);

		$this->assertEquals(false, $table->getField('title', $con));
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

