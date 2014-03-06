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

namespace PSX;

use stdClass;
use PSX\Sql\Condition;
use PSX\Sql\DbTestCase;
use PSX\Sql\Table;
use PSX\Sql\TableInterface;
use PSX\Test\TableDataSet;

/**
 * SqlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SqlTest extends DbTestCase
{
	protected $table = 'psx_sql_test';

	public function getDataSet()
	{
		$dataSet = new TableDataSet();
		$dataSet->addTable(new Table($this->sql, $this->table, array(
			'id'    => TableInterface::TYPE_INT | 10 | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT,
			'title' => TableInterface::TYPE_VARCHAR | 64,
		)), array(
			array('id' => null, 'title' => 'foo'),
			array('id' => null, 'title' => 'bar'),
			array('id' => null, 'title' => 'test'),
		));

		return $dataSet;
	}

	public function testAssoc()
	{
		$actual = $this->sql->assoc('SELECT id, title FROM ' . $this->table . ' WHERE id < ? ORDER BY id ASC', array(3));
		$expect = array(
			array('id' => 1, 'title' => 'foo'),
			array('id' => 2, 'title' => 'bar'),
		);

		$this->assertEquals($expect, $actual);
	}

	public function testAssocObject()
	{
		$actual = $this->sql->assoc('SELECT id, title FROM ' . $this->table . ' WHERE id < ? ORDER BY id ASC', array(3), 'stdClass');

		$row_1 = new stdClass();
		$row_1->id = 1;
		$row_1->title = 'foo';

		$row_2 = new stdClass();
		$row_2->id = 2;
		$row_2->title = 'bar';

		$expect = array($row_1, $row_2);

		$this->assertEquals($expect, $actual);
	}

	public function testAssocMultipleConditions()
	{
		$actual = $this->sql->assoc('SELECT id, title FROM ' . $this->table . ' WHERE id < ? AND id < ? AND id < ? ORDER BY id ASC', array(3, 3, 3));
		$expect = array(
			array('id' => 1, 'title' => 'foo'),
			array('id' => 2, 'title' => 'bar'),
		);

		$this->assertEquals($expect, $actual);
	}

	public function testExecute()
	{
		$actual = $this->sql->execute('DELETE FROM ' . $this->table);

		$this->assertEquals(true, $actual);

		$actual = $this->sql->count($this->table);

		$this->assertEquals(0, $actual);
	}

	public function testGetAll()
	{
		$actual = $this->sql->getAll('SELECT id, title FROM ' . $this->table . ' WHERE id = 1 ORDER BY id ASC');
		$expect = array(array('id' => 1, 'title' => 'foo'));

		$this->assertEquals($expect, $actual);

		$actual = $this->sql->getAll('SELECT id, title FROM ' . $this->table . ' WHERE id = 404');
		$expect = array();

		$this->assertEquals($expect, $actual);
	}

	public function testGetRow()
	{
		$actual = $this->sql->getRow('SELECT id, title FROM ' . $this->table . ' ORDER BY id ASC');
		$expect = array('id' => 1, 'title' => 'foo');

		$this->assertEquals($expect, $actual);

		$actual = $this->sql->getRow('SELECT id, title FROM ' . $this->table . ' WHERE id = 404');
		$expect = array();

		$this->assertEquals($expect, $actual);
	}

	public function testGetCol()
	{
		$actual = $this->sql->getCol('SELECT title FROM ' . $this->table . ' ORDER BY id ASC');
		$expect = array('foo', 'bar', 'test');

		$this->assertEquals($expect, $actual);

		$actual = $this->sql->getRow('SELECT title FROM ' . $this->table . ' WHERE id = 404');
		$expect = array();

		$this->assertEquals($expect, $actual);
	}

	public function testGetField()
	{
		$actual = $this->sql->getField('SELECT id FROM ' . $this->table . ' ORDER BY id ASC');
		$expect = '1';

		$this->assertEquals($expect, $actual);

		$actual = $this->sql->getField('SELECT id FROM ' . $this->table . ' WHERE id = 404');
		$expect = false;

		$this->assertEquals($expect, $actual);
	}

	public function testSelect()
	{
		$actual = $this->sql->select($this->table, array('id', 'title'), new Condition(array('id', '=', 1)), Sql::SELECT_ALL, 'id', Sql::SORT_ASC);
		$expect = array(array('id' => 1, 'title' => 'foo'));

		$this->assertEquals($expect, $actual);

		$actual = $this->sql->select($this->table, array('id', 'title'), null, Sql::SELECT_ROW, 'id', Sql::SORT_ASC);
		$expect = array('id' => 1, 'title' => 'foo');

		$this->assertEquals($expect, $actual);

		$actual = $this->sql->select($this->table, array('title'), null, Sql::SELECT_COL, 'id', Sql::SORT_ASC);
		$expect = array('foo', 'bar', 'test');

		$this->assertEquals($expect, $actual);

		$actual = $this->sql->select($this->table, array('id'), null, Sql::SELECT_FIELD, 'id', Sql::SORT_ASC);
		$expect = '1';

		$this->assertEquals($expect, $actual);

		$actual = $this->sql->select($this->table, array('id'), null, Sql::SELECT_FIELD, 'id', Sql::SORT_DESC);
		$expect = '3';

		$this->assertEquals($expect, $actual);
	}

	public function testInsert()
	{
		$this->sql->insert($this->table, array(
			'title' => 'wusahh',
		));

		$actual = $this->sql->assoc('SELECT id, title FROM ' . $this->table . ' ORDER BY id ASC');
		$expect = array(
			array('id' => 1, 'title' => 'foo'),
			array('id' => 2, 'title' => 'bar'),
			array('id' => 3, 'title' => 'test'),
			array('id' => 4, 'title' => 'wusahh'),
		);

		$this->assertEquals($expect, $actual);
	}

	public function testUpdate()
	{
		$con = new Condition(array('id', '=', 3));

		$this->sql->update($this->table, array(
			'title' => 'yoda',
		), $con);

		$actual = $this->sql->select($this->table, array('title'), $con, Sql::SELECT_FIELD);

		$this->assertEquals('yoda', $actual);
	}

	public function testReplace()
	{
		$con = new Condition(array('id', '=', 3));

		$this->sql->replace($this->table, array(
			'id'    => 3,
			'title' => 'yoda',
		));

		$actual = $this->sql->select($this->table, array('title'), $con, Sql::SELECT_FIELD);

		$this->assertEquals('yoda', $actual);


		$this->sql->replace($this->table, array(
			'id'    => 3,
			'title' => 'wusahhh',
		));

		$actual = $this->sql->select($this->table, array('title'), $con, Sql::SELECT_FIELD);

		$this->assertEquals('wusahhh', $actual);
	}

	public function testDelete()
	{
		$con = new Condition(array('id', '=', 2));

		$this->sql->delete($this->table, $con);

		$actual = $this->sql->assoc('SELECT id, title FROM ' . $this->table . ' ORDER BY id ASC');
		$expect = array(
			array('id' => 1, 'title' => 'foo'),
			array('id' => 3, 'title' => 'test'),
		);

		$this->assertEquals($expect, $actual);
	}

	public function testQuote()
	{
		$actual = $this->sql->quote("foo'ba\"r");
		$expect = "'foo\'ba\\\"r'";

		$this->assertEquals($expect, $actual);

		$actual = $this->sql->quote("Co'mpl''ex \"st'\"ring");
		$expect = "'Co\\'mpl\\'\\'ex \\\"st\\'\\\"ring'";

		$this->assertEquals($expect, $actual);
	}
}