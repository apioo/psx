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

namespace PSX\Handler;

use PSX\Data\ResultSet;
use PSX\Data\Record;
use PSX\DateTime;
use PSX\Sql;
use PSX\Sql\Condition;
use PSX\Sql\DbTestCase;
use PSX\Sql\Join;
use PSX\Sql\Table;
use PSX\Sql\TableAbstract;
use PSX\Test\TableDataSet;

/**
 * HandlerTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait HandlerTestCase
{
	/**
	 * Returns the handler wich should be used for the test. The handler must
	 * have the following fields: id, userId, title, date. And the following
	 * default values:
	 * <code>
	 * 	id = 1,
	 * 	userId = 1,
	 * 	title = 'foo',
	 * 	date = '2013-04-29 16:56:32'
	 *
	 * 	id = 2,
	 * 	userId = 1,
	 * 	title = 'bar',
	 * 	date = '2013-04-29 16:56:32'
	 *
	 * 	id = 3,
	 * 	userId = 2,
	 * 	title = 'test',
	 * 	date = '2013-04-29 16:56:32'
	 *
	 * 	id = 4,
	 * 	userId = 3,
	 * 	title = 'blub',
	 * 	date = '2013-04-29 16:56:32'
	 * </code>
	 *
	 * @return PSX\Handler\HandlerInterface
	 */
	protected function getHandler()
	{
		$this->markTestIncomplete('Handler not given');
	}

	public function testGetAll()
	{
		$handler = $this->getHandler();

		// test select query
		$result = $handler->getAll(array('id', 'title'));

		$this->assertEquals(true, is_array($result));
		$this->assertEquals(4, count($result));

		foreach($result as $row)
		{
			$this->assertInstanceOf('PSX\Data\Record', $row);
			$this->assertEquals(true, $row->getId() != null);
			$this->assertEquals(true, $row->getUserId() == null);
			$this->assertEquals(true, $row->getTitle() != null);
			$this->assertEquals(true, $row->getDate() == null);
		}

		// test all query
		$result = $handler->getAll();

		$this->assertEquals(true, is_array($result));
		$this->assertEquals(4, count($result));

		foreach($result as $row)
		{
			$this->assertInstanceOf('PSX\Data\Record', $row);
			$this->assertEquals(true, $row->getId() != null);
			$this->assertEquals(true, $row->getUserId() != null);
			$this->assertEquals(true, $row->getTitle() != null);
			$this->assertInstanceOf('DateTime', $row->getDate());
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
			$this->assertInstanceOf('PSX\Data\Record', $row);
			$this->assertEquals(true, $row->getId() != null);
			$this->assertEquals(true, $row->getTitle() != null);
		}

		// test count
		$result = $handler->getAll(array('id', 'title'), 0, 2);

		$this->assertEquals(true, is_array($result));
		$this->assertEquals(2, count($result));

		foreach($result as $row)
		{
			$this->assertInstanceOf('PSX\Data\Record', $row);
			$this->assertEquals(true, $row->getId() != null);
			$this->assertEquals(true, $row->getTitle() != null);
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
			$this->assertInstanceOf('PSX\Data\Record', $row);
			$this->assertEquals(true, $row->getId() != null);
			$this->assertEquals(true, $row->getTitle() != null);
		}

		// check order
		$this->assertEquals(4, $result[0]->getId());
		$this->assertEquals(3, $result[1]->getId());
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
			$this->assertInstanceOf('PSX\Data\Record', $row);
			$this->assertEquals(true, $row->getId() != null);
			$this->assertEquals(true, $row->getTitle() != null);
		}

		// check order
		$this->assertEquals(2, $result[0]->getId());
		$this->assertEquals(1, $result[1]->getId());

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
			$this->assertInstanceOf('PSX\Data\Record', $row);
			$this->assertEquals(true, $row->getId() != null);
			$this->assertEquals(true, $row->getTitle() != null);
		}

		// check order
		$this->assertEquals(4, $result[0]->getId());
		$this->assertEquals(2, $result[1]->getId());
		$this->assertEquals(1, $result[2]->getId());
	}

	public function testGetBy()
	{
		$handler = $this->getHandler();

		$result = $handler->getByUserId(1, array('id', 'title'));

		foreach($result as $row)
		{
			$this->assertInstanceOf('PSX\Data\Record', $row);
			$this->assertEquals(true, $row->getId() != null);
			$this->assertEquals(true, $row->getUserId() == null);
			$this->assertEquals(true, $row->getTitle() != null);
			$this->assertEquals(true, $row->getDate() == null);
		}
	}

	public function testGetOneBy()
	{
		$handler = $this->getHandler();

		$row = $handler->getOneById(1, array('id', 'title'));

		$this->assertInstanceOf('PSX\Data\Record', $row);
		$this->assertEquals(true, $row->getId() != null);
		$this->assertEquals(true, $row->getUserId() == null);
		$this->assertEquals(true, $row->getTitle() != null);
		$this->assertEquals(true, $row->getDate() == null);
	}

	public function testGet()
	{
		$handler = $this->getHandler();

		$row = $handler->get(1, array('id', 'title'));

		$this->assertInstanceOf('PSX\Data\Record', $row);
		$this->assertEquals(true, $row->getId() != null);
		$this->assertEquals(true, $row->getUserId() == null);
		$this->assertEquals(true, $row->getTitle() != null);
		$this->assertEquals(true, $row->getDate() == null);
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
			$this->assertInstanceOf('PSX\Data\Record', $row);
			$this->assertEquals(true, $row->getId() != null);
			$this->assertEquals(true, $row->getTitle() != null);
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

		$this->assertInstanceOf('PSX\Data\Record', $obj);

		// existing record
		$obj = $handler->getRecord(1);

		$this->assertInstanceOf('PSX\Data\Record', $obj);
		$this->assertEquals(1, $obj->getId());
		$this->assertEquals(1, $obj->getUserId());
		$this->assertEquals('foo', $obj->getTitle());
		$this->assertInstanceOf('DateTime', $obj->getDate());
	}

	public function testCreate()
	{
		$handler = $this->getHandler();

		$record = $handler->getRecord();
		$record->setUserId(2);
		$record->setTitle('foobar');
		$record->setDate(new DateTime());

		$handler->create($record);

		$record = $handler->get(5);

		$this->assertEquals(2, $record->getUserId());
		$this->assertEquals('foobar', $record->getTitle());
		$this->assertInstanceOf('DateTime', $record->getDate());
	}

	public function testUpdate()
	{
		$handler = $this->getHandler();

		$record = $handler->getRecord(1);
		$record->setUserId(2);
		$record->setTitle('foobar');
		$record->setDate(new DateTime());

		$handler->update($record);

		$record = $handler->get(1);

		$this->assertEquals(2, $record->getUserId());
		$this->assertEquals('foobar', $record->getTitle());
		$this->assertInstanceOf('DateTime', $record->getDate());
	}

	public function testDelete()
	{
		$handler = $this->getHandler();

		$record = $handler->getRecord(1);
		$record->setUserId(2);
		$record->setTitle('foobar');
		$record->setDate(new DateTime());

		$handler->delete($record);

		$record = $handler->get(1);

		$this->assertEmpty($record);
	}
}
