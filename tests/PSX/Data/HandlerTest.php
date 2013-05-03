<?php
/*
 *  $Id: ResultSetTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Data;

use PSX\Data\ResultSet;
use PSX\DateTime;
use PSX\Sql;
use PSX\Sql\Condition;
use PSX\Sql\DbTestCase;
use PSX\Sql\Join;
use PSX\Sql\TableAbstract;
use PSX\Test\TableDataSet;

/**
 * PSX_Data_ResultSetTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class HandlerTest extends DbTestCase
{
	protected $comment;

	public function getDataSet()
	{
		$this->comment = new Comment($this->sql);

		$dataSet = new TableDataSet();
		$dataSet->addTable($this->comment, array(
			array('id' => null, 'userId' => 1, 'title' => 'foo', 'date' => date(DateTime::SQL, 1367247392)),
			array('id' => null, 'userId' => 1, 'title' => 'bar', 'date' => date(DateTime::SQL, 1367247392)),
			array('id' => null, 'userId' => 2, 'title' => 'test', 'date' => date(DateTime::SQL, 1367247392)),
			array('id' => null, 'userId' => 3, 'title' => 'blub', 'date' => date(DateTime::SQL, 1367247392)),
		));

		return $dataSet;
	}

	public function testGetAll()
	{
		$handler = $this->getHandler();

		// test simple query
		$result = $handler->getAll(array('id', 'title'));

		$this->assertEquals(true, is_array($result));
		$this->assertEquals(4, count($result));

		foreach($result as $row)
		{
			$this->assertEquals(2, count($row));
			$this->assertEquals(true, isset($row['id']));
			$this->assertEquals(true, isset($row['title']));
		}
	}

	public function testGetAllLimit()
	{
		$handler = $this->getHandler();

		// test start index
		$result = $handler->getAll(array('id', 'title'), 3);

		$this->assertEquals(true, is_array($result));
		$this->assertEquals(1, count($result));

		foreach($result as $row)
		{
			$this->assertEquals(2, count($row));
			$this->assertEquals(true, isset($row['id']));
			$this->assertEquals(true, isset($row['title']));
		}

		// test count
		$result = $handler->getAll(array('id', 'title'), 0, 2);

		$this->assertEquals(true, is_array($result));
		$this->assertEquals(2, count($result));

		foreach($result as $row)
		{
			$this->assertEquals(2, count($row));
			$this->assertEquals(true, isset($row['id']));
			$this->assertEquals(true, isset($row['title']));
		}
	}

	public function testGetAllSort()
	{
		$handler = $this->getHandler();

		// test sort by
		$result = $handler->getAll(array('id', 'title'), 0, 2, 'id', Sql::SORT_DESC);

		$this->assertEquals(true, is_array($result));
		$this->assertEquals(2, count($result));

		foreach($result as $row)
		{
			$this->assertEquals(2, count($row));
			$this->assertEquals(true, isset($row['id']));
			$this->assertEquals(true, isset($row['title']));
		}

		// check order
		$this->assertEquals(4, $result[0]['id']);
		$this->assertEquals(3, $result[1]['id']);
	}

	public function testGetAllCondition()
	{
		$handler = $this->getHandler();

		// test condition
		$con    = new Condition(array('userId', '=', 1));
		$result = $handler->getAll(array('id', 'title'), 0, 16, 'id', Sql::SORT_DESC, $con);

		$this->assertEquals(true, is_array($result));
		$this->assertEquals(2, count($result));

		foreach($result as $row)
		{
			$this->assertEquals(2, count($row));
			$this->assertEquals(true, isset($row['id']));
			$this->assertEquals(true, isset($row['title']));
		}

		// check order
		$this->assertEquals(2, $result[0]['id']);
		$this->assertEquals(1, $result[1]['id']);

		// test and condition
		$con    = new Condition();
		$con->add('userId', '=', 1, 'AND');
		$con->add('userId', '=', 3);
		$result = $handler->getAll(array('id', 'title'), 0, 16, 'id', Sql::SORT_DESC, $con);

		$this->assertEquals(true, is_array($result));
		$this->assertEquals(0, count($result));

		// test or condition
		$con    = new Condition();
		$con->add('userId', '=', 1, 'OR');
		$con->add('userId', '=', 3);
		$result = $handler->getAll(array('id', 'title'), 0, 16, 'id', Sql::SORT_DESC, $con);

		$this->assertEquals(true, is_array($result));
		$this->assertEquals(3, count($result));

		foreach($result as $row)
		{
			$this->assertEquals(2, count($row));
			$this->assertEquals(true, isset($row['id']));
			$this->assertEquals(true, isset($row['title']));
		}

		// check order
		$this->assertEquals(4, $result[0]['id']);
		$this->assertEquals(2, $result[1]['id']);
		$this->assertEquals(1, $result[2]['id']);
	}

	public function testGetAllMode()
	{
		$handler = $this->getHandler();

		// test mode
		$result = $handler->getAll(array('id', 'title'), 0, 16, 'id', Sql::SORT_DESC, null, Sql::FETCH_OBJECT);

		$this->assertEquals(true, is_array($result));
		$this->assertEquals(4, count($result));

		foreach($result as $row)
		{
			$this->assertInstanceOf('stdClass', $row);
			$this->assertEquals(true, isset($row->id));
			$this->assertEquals(true, isset($row->title));
		}
	}

	public function testGetBy()
	{
		$handler = $this->getHandler();

		$result = $handler->getByUserId(1, array('id', 'title'));

		foreach($result as $row)
		{
			$this->assertEquals(true, is_array($row));
			$this->assertEquals(true, isset($row['id']));
			$this->assertEquals(false, isset($row['userId']));
			$this->assertEquals(true, isset($row['title']));
			$this->assertEquals(false, isset($row['date']));
		}

		$result = $handler->getByUserId(1, array('id', 'title'), Sql::FETCH_OBJECT);

		foreach($result as $obj)
		{
			$this->assertInstanceOf('stdClass', $obj);
			$this->assertEquals(true, isset($obj->id));
			$this->assertEquals(false, isset($obj->userId));
			$this->assertEquals(true, isset($obj->title));
			$this->assertEquals(false, isset($obj->date));
		}
	}

	public function testGetOneBy()
	{
		$handler = $this->getHandler();

		$row = $handler->getOneById(1, array('id', 'title'));

		$this->assertEquals(true, is_array($row));
		$this->assertEquals(true, isset($row['id']));
		$this->assertEquals(false, isset($row['userId']));
		$this->assertEquals(true, isset($row['title']));
		$this->assertEquals(false, isset($row['date']));

		$obj = $handler->getOneById(1, array('id', 'title'), Sql::FETCH_OBJECT);

		$this->assertInstanceOf('stdClass', $obj);
		$this->assertEquals(true, isset($obj->id));
		$this->assertEquals(false, isset($obj->userId));
		$this->assertEquals(true, isset($obj->title));
		$this->assertEquals(false, isset($obj->date));
	}

	public function testGet()
	{
		$handler = $this->getHandler();

		$row = $handler->get(1, array('id', 'title'));

		$this->assertEquals(true, is_array($row));
		$this->assertEquals(true, isset($row['id']));
		$this->assertEquals(false, isset($row['userId']));
		$this->assertEquals(true, isset($row['title']));
		$this->assertEquals(false, isset($row['date']));

		$obj = $handler->get(1, array('id', 'title'), Sql::FETCH_OBJECT);

		$this->assertInstanceOf('stdClass', $obj);
		$this->assertEquals(true, isset($obj->id));
		$this->assertEquals(false, isset($obj->userId));
		$this->assertEquals(true, isset($obj->title));
		$this->assertEquals(false, isset($obj->date));
	}

	public function testGetResultSet()
	{
		$handler = $this->getHandler();

		$result = $handler->getResultSet(array('id', 'title'), 0, 2, 'id', Sql::SORT_DESC);

		$this->assertInstanceOf('\PSX\Data\ResultSet', $result);
		$this->assertEquals(2, count($result));
		$this->assertEquals(4, $result->getTotalResults());

		foreach($result as $row)
		{
			$this->assertEquals(2, count($row));
			$this->assertEquals(true, isset($row['id']));
			$this->assertEquals(true, isset($row['title']));
		}
	}

	public function testGetSupportedFields()
	{
		$handler = $this->getHandler();
		$fields  = $handler->getSupportedFields();

		$this->assertEquals(array('id', 'userId', 'title', 'date'), $fields);
	}

	public function testGetCount()
	{
		$handler = $this->getHandler();

		$this->assertEquals(4, $handler->getCount());
		$this->assertEquals(2, $handler->getCount(new Condition(array('userId', '=', 1))));
	}

	public function testGetRecord()
	{
		$handler = $this->getHandler();

		// new record
		$obj = $handler->getRecord();

		$this->assertInstanceOf('stdClass', $obj);

		// existing record
		$obj = $handler->getRecord(1);

		$this->assertInstanceOf('stdClass', $obj);
		$this->assertEquals(1, $obj->id);
		$this->assertEquals(1, $obj->userId);
		$this->assertEquals('foo', $obj->title);
		$this->assertEquals(date(DateTime::SQL, 1367247392), $obj->date);
	}

	public function testGetClassName()
	{
		$handler = $this->getHandler();

		$this->assertEquals('stdClass', $handler->getClassName());
	}

	protected function getHandler()
	{
		return new Handler($this->comment);
	}
}

class Handler extends HandlerAbstract
{
	public function getClassName()
	{
		return 'stdClass';
	}
}

class Comment extends TableAbstract
{
	public function getConnections()
	{
		return array();
	}

	public function getName()
	{
		return 'psx_handler_comment';
	}

	public function getColumns()
	{
		return array(
			'id'     => self::TYPE_INT | 10 | self::PRIMARY_KEY | self::AUTO_INCREMENT,
			'userId' => self::TYPE_INT | 10,
			'title'  => self::TYPE_VARCHAR | 32,
			'date'   => self::TYPE_DATETIME,
		);
	}
}

