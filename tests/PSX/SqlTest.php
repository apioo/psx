<?php
/*
 *  $Id: SqlTest.php 596 2012-08-15 22:24:44Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * PSX_SqlTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 596 $
 */
class SqlTest extends \PHPUnit_Framework_TestCase
{
	protected $sql;
	protected $table;

	protected function setUp()
	{
		try
		{
			$config = getConfig();

			$this->table = 'PSX_Sql';
			$this->sql   = new Sql($config['psx_sql_host'],
				$config['psx_sql_user'],
				$config['psx_sql_pw'],
				$config['psx_sql_db']);

		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `{$this->table}` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8
SQL;

			$this->sql->exec($sql);

			$this->resetTable();
		}
		catch(\Exception $e)
		{
			$this->markTestSkipped($e->getMessage());
		}
	}

	protected function tearDown()
	{
		if($this->sql instanceof Sql)
		{
			$this->sql->exec('TRUNCATE TABLE ' . $this->table);
		}

		unset($this->table);
		unset($this->sql);
	}

	public function testAssoc()
	{
		$r = $this->sql->assoc('SELECT id, title FROM ' . $this->table . ' WHERE id < ? ORDER BY id ASC', array(3));

		$e = array(

			array(

				'id'    => 1,
				'title' => 'foo',

			),

			array(

				'id'    => 2,
				'title' => 'bar',

			),

		);

		$this->assertEquals($e, $r);
	}

	public function testAssocObject()
	{
		$r = $this->sql->assoc('SELECT id, title FROM ' . $this->table . ' WHERE id < ? ORDER BY id ASC', array(3), 'stdClass');

		$row_1 = new stdClass();
		$row_1->id = 1;
		$row_1->title = 'foo';

		$row_2 = new stdClass();
		$row_2->id = 2;
		$row_2->title = 'bar';

		$e = array($row_1, $row_2);

		$this->assertEquals($e, $r);
	}

	public function testExecute()
	{
		$r = $this->sql->execute('DELETE FROM ' . $this->table);

		$this->assertEquals(true, $r);


		$this->resetTable();
	}

	public function testGetAll()
	{
		$r = $this->sql->getAll('SELECT id, title FROM ' . $this->table . ' WHERE id = 1 ORDER BY id ASC');

		$this->assertEquals(array(array('id' => 1, 'title' => 'foo')), $r);

		$r = $this->sql->getAll('SELECT id, title FROM ' . $this->table . ' WHERE id = 404');

		$this->assertEquals(array(), $r);
	}

	public function testGetRow()
	{
		$r = $this->sql->getRow('SELECT id, title FROM ' . $this->table . ' ORDER BY id ASC');

		$this->assertEquals(array('id' => 1, 'title' => 'foo'), $r);

		$r = $this->sql->getRow('SELECT id, title FROM ' . $this->table . ' WHERE id = 404');

		$this->assertEquals(array(), $r);
	}

	public function testGetCol()
	{
		$r = $this->sql->getCol('SELECT title FROM ' . $this->table . ' ORDER BY id ASC');

		$this->assertEquals(array('foo', 'bar', 'test'), $r);

		$r = $this->sql->getRow('SELECT title FROM ' . $this->table . ' WHERE id = 404');

		$this->assertEquals(array(), $r);
	}

	public function testGetField()
	{
		$r = $this->sql->getField('SELECT id FROM ' . $this->table . ' ORDER BY id ASC');

		$this->assertEquals('1', $r);

		$r = $this->sql->getField('SELECT id FROM ' . $this->table . ' WHERE id = 404');

		$this->assertEquals(false, $r);
	}

	public function testSelect()
	{
		$r = $this->sql->select($this->table, array('id', 'title'), new Condition(array('id', '=', 1)), Sql::SELECT_ALL, 'id', Sql::SORT_ASC);

		$this->assertEquals(array(array('id' => 1, 'title' => 'foo')), $r);

		$r = $this->sql->select($this->table, array('id', 'title'), null, Sql::SELECT_ROW, 'id', Sql::SORT_ASC);

		$this->assertEquals(array('id' => 1, 'title' => 'foo'), $r);

		$r = $this->sql->select($this->table, array('title'), null, Sql::SELECT_COL, 'id', Sql::SORT_ASC);

		$this->assertEquals(array('foo', 'bar', 'test'), $r);

		$r = $this->sql->select($this->table, array('id'), null, Sql::SELECT_FIELD, 'id', Sql::SORT_ASC);

		$this->assertEquals('1', $r);

		$r = $this->sql->select($this->table, array('id'), null, Sql::SELECT_FIELD, 'id', Sql::SORT_DESC);

		$this->assertEquals('3', $r);
	}

	public function testInsert()
	{
		$this->sql->insert($this->table, array(

			'title' => 'wusahh',

		));

		$r = $this->sql->assoc('SELECT id, title FROM ' . $this->table . ' ORDER BY id ASC');

		$e = array(

			array(

				'id'    => 1,
				'title' => 'foo',

			),

			array(

				'id'    => 2,
				'title' => 'bar',

			),

			array(

				'id'    => 3,
				'title' => 'test',

			),

			array(

				'id'    => 4,
				'title' => 'wusahh',

			),

		);

		$this->assertEquals($e, $r);


		$this->resetTable();
	}

	public function testUpdate()
	{
		$con = new Condition(array('id', '=', 3));

		$this->sql->update($this->table, array(

			'title' => 'yoda',

		), $con);

		$r = $this->sql->select($this->table, array('title'), $con, Sql::SELECT_FIELD);

		$this->assertEquals('yoda', $r);


		$this->resetTable();
	}

	public function testReplace()
	{
		$con = new Condition(array('id', '=', 3));

		$this->sql->replace($this->table, array(

			'id'    => 3,
			'title' => 'yoda',

		));

		$r = $this->sql->select($this->table, array('title'), $con, Sql::SELECT_FIELD);

		$this->assertEquals('yoda', $r);


		$this->sql->replace($this->table, array(

			'id'    => 3,
			'title' => 'wusahhh',

		));

		$r = $this->sql->select($this->table, array('title'), $con, Sql::SELECT_FIELD);

		$this->assertEquals('wusahhh', $r);


		$this->resetTable();
	}

	public function testDelete()
	{
		$this->sql->delete($this->table, new Condition(array('id', '=', 2)));

		$r = $this->sql->assoc('SELECT id, title FROM ' . $this->table . ' ORDER BY id ASC');

		$e = array(

			array(

				'id'    => 1,
				'title' => 'foo',

			),

			array(

				'id'    => 3,
				'title' => 'test',

			),

		);

		$this->assertEquals($e, $r);


		$this->resetTable();
	}

	public function testQuote()
	{
		$r = $this->sql->quote("foo'ba\"r");
		$e = "'foo\'ba\\\"r'";

		$this->assertEquals($e, $r);

		$r = $this->sql->quote("Co'mpl''ex \"st'\"ring");
		$e = "'Co\\'mpl\\'\\'ex \\\"st\\'\\\"ring'";

		$this->assertEquals($e, $r);
	}

	private function resetTable()
	{
		$this->sql->exec('TRUNCATE TABLE ' . $this->table);

		$data = array(

			array('title' => 'foo'),
			array('title' => 'bar'),
			array('title' => 'test'),

		);

		foreach($data as $d)
		{
			$this->sql->insert($this->table, $d);
		}
	}
}