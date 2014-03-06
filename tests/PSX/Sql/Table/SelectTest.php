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

namespace PSX\Sql\Table;

use PSX\Data\ResultSet;
use PSX\DateTime;
use PSX\Sql;
use PSX\Sql\DbTestCase;
use PSX\Sql\Join;
use PSX\Sql\TableAbstract;
use PSX\Test\TableDataSet;

/**
 * SelectTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SelectTest extends DbTestCase
{
	protected $tableNews = 'psx_sql_table_select_news';
	protected $tableUser = 'psx_sql_table_select_user';
	protected $tableUserNews = 'psx_sql_table_select_usernews';

	public function getDataSet()
	{
		$dataSet = new TableDataSet();
		$dataSet->addTable(new SelectTestNews($this->sql), array(
			array('id' => null, 'userId' => 1, 'title' => 'foo', 'date' => date(DateTime::SQL)),
			array('id' => null, 'userId' => 1, 'title' => 'bar', 'date' => date(DateTime::SQL)),
			array('id' => null, 'userId' => 2, 'title' => 'test', 'date' => date(DateTime::SQL)),
			array('id' => null, 'userId' => 3, 'title' => 'blub', 'date' => date(DateTime::SQL)),
		));

		$dataSet->addTable(new SelectTestUser($this->sql), array(
			array('id' => null, 'groupId' => 1, 'name' => 'foo'),
			array('id' => null, 'groupId' => 1, 'name' => 'bar'),
		));

		$dataSet->addTable(new SelectTestUserNews($this->sql), array(
			array('id' => null, 'userId' => 1, 'newsId' => 1),
			array('id' => null, 'userId' => 1, 'newsId' => 2),
			array('id' => null, 'userId' => 1, 'newsId' => 3),
			array('id' => null, 'userId' => 1, 'newsId' => 4),
			array('id' => null, 'userId' => 2, 'newsId' => 1),
			array('id' => null, 'userId' => 2, 'newsId' => 2),
		));

		$dataSet->addTable(new SelectTestGroup($this->sql), array(
			array('id' => null, 'name' => 'test'),
		));

		return $dataSet;
	}

	public function testJoin()
	{
		// inner
		$news   = new SelectTestNews($this->sql);
		$user   = new SelectTestUser($this->sql);
		$result = $news->select(array('id', 'userId', 'title'))
			->join(Join::INNER, $user
				->select(array('id', 'name'), 'user')
			)
			->getAll();

		$this->assertEquals(3, count($result));

		// left
		$news   = new SelectTestNews($this->sql);
		$user   = new SelectTestUser($this->sql);
		$result = $news->select(array('id', 'userId', 'title'))
			->join(Join::LEFT, $user
				->select(array('id', 'name'), 'user')
			)
			->getAll();

		$this->assertEquals(4, count($result));

		// right
		$news   = new SelectTestNews($this->sql);
		$user   = new SelectTestUser($this->sql);
		$result = $news->select(array('id', 'userId', 'title'))
			->join(Join::RIGHT, $user
				->select(array('id', 'name'), 'user')
			)
			->getAll();

		$this->assertEquals(3, count($result));
	}

	public function testWhere()
	{
		$news = new SelectTestNews($this->sql);

		// where
		$result = $news->select(array('id', 'userId', 'title'))
			->where('userId', '=', 1)
			->getAll();

		$this->assertEquals(2, count($result));

		// or where
		$result = $news->select(array('id', 'userId', 'title'))
			->where('userId', '=', 3, 'OR')
			->where('userId', '=', 1)
			->getAll();

		$this->assertEquals(3, count($result));

		// and where
		$result = $news->select(array('id', 'userId', 'title'))
			->where('userId', '=', 1, 'AND')
			->where('title', '=', 'foo')
			->getAll();

		$this->assertEquals(1, count($result));
	}

	public function testGroupBy()
	{
		$news = new SelectTestNews($this->sql);

		$result = $news->select(array('id', 'userId', 'title'))
			->groupBy('userId')
			->getAll();

		$this->assertEquals(3, count($result));
	}

	public function testOrderBy()
	{
		$user = new SelectTestUser($this->sql);

		$result = $user->select(array('id', 'name'))
			->orderBy('name')
			->getAll();

		$this->assertEquals('foo', $result[0]['name']);
		$this->assertEquals('bar', $result[1]['name']);

		$result = $user->select(array('id', 'name'))
			->orderBy('name', Sql::SORT_ASC)
			->getAll();

		$this->assertEquals('bar', $result[0]['name']);
		$this->assertEquals('foo', $result[1]['name']);

		$result = $user->select(array('id', 'name'))
			->orderBy('name', Sql::SORT_DESC)
			->getAll();

		$this->assertEquals('foo', $result[0]['name']);
		$this->assertEquals('bar', $result[1]['name']);
	}

	public function testLimit()
	{
		$user = new SelectTestUser($this->sql);

		$result = $user->select(array('id', 'name'))
			->limit(1)
			->getAll();

		$this->assertEquals(1, count($result));
		$this->assertEquals('foo', $result[0]['name']);

		$result = $user->select(array('id', 'name'))
			->limit(0, 1)
			->getAll();

		$this->assertEquals(1, count($result));
		$this->assertEquals('foo', $result[0]['name']);

		$result = $user->select(array('id', 'name'))
			->limit(1, 1)
			->getAll();

		$this->assertEquals(1, count($result));
		$this->assertEquals('bar', $result[0]['name']);
	}

	public function testJoinCardinality()
	{
		// test normal join
		$news     = new SelectTestNews($this->sql);
		$user     = new SelectTestUser($this->sql);
		$userNews = new SelectTestUserNews($this->sql);

		$result = $userNews->select(array('userId'))
			->join(Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->join(Join::INNER, $news
				->select(array('id', 'title'), 'news')
			)
			->getAll();

		$this->assertEquals(6, count($result));

		foreach($result as $row)
		{
			$this->assertArrayHasKey('userId', $row);
			$this->assertArrayHasKey('newsId', $row);
			$this->assertArrayHasKey('clientId', $row);
			$this->assertArrayHasKey('clientName', $row);
			$this->assertArrayHasKey('newsId', $row);
			$this->assertArrayHasKey('newsTitle', $row);
		}

		// test 1:n join
		$news     = new SelectTestNews($this->sql);
		$userNews = new SelectTestUserNews($this->sql);

		$result = $news->select(array('id', 'title'))
			->join(Join::INNER, $userNews
				->select(array('userId', 'newsId'), 'foo')
			, '1:n')
			->getAll();

		$this->assertEquals(6, count($result));

		foreach($result as $row)
		{
			$this->assertArrayHasKey('id', $row);
			$this->assertArrayHasKey('title', $row);
			$this->assertArrayHasKey('fooUserId', $row);
			$this->assertArrayHasKey('fooNewsId', $row);
		}

		// test n:1 join
		$news     = new SelectTestNews($this->sql);
		$userNews = new SelectTestUserNews($this->sql);

		$result = $userNews->select(array('userId', 'newsId'))
			->join(Join::INNER, $news
				->select(array('id', 'title'), 'foo')
			, 'n:1')
			->getAll();

		$this->assertEquals(6, count($result));

		foreach($result as $row)
		{
			$this->assertArrayHasKey('userId', $row);
			$this->assertArrayHasKey('newsId', $row);
			$this->assertArrayHasKey('fooId', $row);
			$this->assertArrayHasKey('fooTitle', $row);
		}
	}

	public function testGetAll()
	{
		// array
		$news   = new SelectTestNews($this->sql);
		$user   = new SelectTestUser($this->sql);
		$result = $news->select(array('id', 'userId', 'title'))
			->join(Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->orderBy('clientId', Sql::SORT_DESC)
			->limit(1)
			->getAll();

		$row = current($result);

		$this->assertEquals(true, is_array($row));
		$this->assertArrayHasKey('id', $row);
		$this->assertArrayHasKey('userId', $row);
		$this->assertArrayHasKey('title', $row);
		$this->assertArrayHasKey('clientId', $row);
		$this->assertArrayHasKey('clientName', $row);

		// default object
		$news   = new SelectTestNews($this->sql);
		$user   = new SelectTestUser($this->sql);
		$result = $news->select(array('id', 'userId', 'title'))
			->join(Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->orderBy('id', Sql::SORT_DESC)
			->limit(1)
			->getAll(Sql::FETCH_OBJECT);

		$row = current($result);

		$this->assertInstanceOf('stdClass', $row);
		$this->assertObjectHasAttribute('id', $row);
		$this->assertObjectHasAttribute('userId', $row);
		$this->assertObjectHasAttribute('title', $row);
		$this->assertObjectHasAttribute('clientId', $row);
		$this->assertObjectHasAttribute('clientName', $row);

		// custom object
		$news   = new SelectTestNews($this->sql);
		$user   = new SelectTestUser($this->sql);
		$args   = array('foo', 'bar');
		$result = $news->select(array('id', 'userId', 'title'))
			->join(Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->orderBy('id', Sql::SORT_DESC)
			->limit(1)
			->getAll(Sql::FETCH_OBJECT, '\PSX\Sql\Table\SelectTestRecord', $args);

		$row = current($result);

		$this->assertInstanceOf('\PSX\Sql\Table\SelectTestRecord', $row);
		$this->assertObjectHasAttribute('id', $row);
		$this->assertObjectHasAttribute('userId', $row);
		$this->assertObjectHasAttribute('title', $row);
		$this->assertObjectHasAttribute('clientId', $row);
		$this->assertObjectHasAttribute('clientName', $row);
		$this->assertEquals($args, $row->getArgs());
	}

	public function testGetRow()
	{
		// array
		$news = new SelectTestNews($this->sql);
		$user = new SelectTestUser($this->sql);
		$row  = $news->select(array('id', 'userId', 'title'))
			->join(Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->limit(1)
			->getRow();

		$this->assertEquals(true, is_array($row));
		$this->assertArrayHasKey('id', $row);
		$this->assertArrayHasKey('userId', $row);
		$this->assertArrayHasKey('title', $row);
		$this->assertArrayHasKey('clientId', $row);
		$this->assertArrayHasKey('clientName', $row);

		// default object
		$news = new SelectTestNews($this->sql);
		$user = new SelectTestUser($this->sql);
		$row  = $news->select(array('id', 'userId', 'title'))
			->join(Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->limit(1)
			->getRow(Sql::FETCH_OBJECT);

		$this->assertInstanceOf('stdClass', $row);
		$this->assertObjectHasAttribute('id', $row);
		$this->assertObjectHasAttribute('userId', $row);
		$this->assertObjectHasAttribute('title', $row);
		$this->assertObjectHasAttribute('clientId', $row);
		$this->assertObjectHasAttribute('clientName', $row);

		// custom object
		$news = new SelectTestNews($this->sql);
		$user = new SelectTestUser($this->sql);
		$args = array('foo', 'bar');
		$row  = $news->select(array('id', 'userId', 'title'))
			->join(Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->limit(1)
			->getRow(Sql::FETCH_OBJECT, '\PSX\Sql\Table\SelectTestRecord', $args);

		$this->assertInstanceOf('\PSX\Sql\Table\SelectTestRecord', $row);
		$this->assertObjectHasAttribute('id', $row);
		$this->assertObjectHasAttribute('userId', $row);
		$this->assertObjectHasAttribute('title', $row);
		$this->assertObjectHasAttribute('clientId', $row);
		$this->assertObjectHasAttribute('clientName', $row);
		$this->assertEquals($args, $row->getArgs());
	}

	public function testDeepJoins()
	{
		$news     = new SelectTestNews($this->sql);
		$userNews = new SelectTestUserNews($this->sql);
		$user     = new SelectTestUser($this->sql);
		$group    = new SelectTestGroup($this->sql);
		$result   = $news->select(array('id', 'userId', 'title'))
			->join(Join::INNER, $userNews
				->select(array(), 'userNews')
				->join(Join::INNER, $user
					->select(array('name'), 'user')
					->join(Join::INNER, $group
						->select(array('name'), 'group')
					)
				)
			, '1:n')
			->getAll();

		foreach($result as $row)
		{
			$this->assertArrayHasKey('id', $row);
			$this->assertArrayHasKey('userId', $row);
			$this->assertArrayHasKey('title', $row);
			$this->assertArrayHasKey('userName', $row);
			$this->assertArrayHasKey('groupName', $row);
		}
	}

	public function testNoPrimaryPrefix()
	{
		$news     = new SelectTestNews($this->sql);
		$userNews = new SelectTestUserNews($this->sql);
		$user     = new SelectTestUser($this->sql);
		$group    = new SelectTestGroup($this->sql);
		$result   = $news->select(array('id', 'userId', 'title'), 'news')
			->join(Join::INNER, $userNews
				->select(array(), 'userNews')
				->join(Join::INNER, $user
					->select(array('name'))
					->join(Join::INNER, $group
						->select(array('name'), 'group')
					)
				)
			, '1:n')
			->getAll();

		foreach($result as $row)
		{
			$this->assertArrayHasKey('newsId', $row);
			$this->assertArrayHasKey('newsUserId', $row);
			$this->assertArrayHasKey('newsTitle', $row);
			$this->assertArrayHasKey('name', $row);
			$this->assertArrayHasKey('groupName', $row);
		}
	}
}

class SelectTestNews extends TableAbstract
{
	public function getConnections()
	{
		return array(
			'userId' => 'psx_sql_table_select_user',
		);
	}

	public function getName()
	{
		return 'psx_sql_table_select_news';
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

class SelectTestUser extends TableAbstract
{
	public function getConnections()
	{
		return array(
			'groupId' => 'psx_sql_table_select_group',
		);
	}

	public function getName()
	{
		return 'psx_sql_table_select_user';
	}

	public function getColumns()
	{
		return array(
			'id'      => self::TYPE_INT | 10 | self::PRIMARY_KEY | self::AUTO_INCREMENT,
			'groupId' => self::TYPE_INT | 10,
			'name'    => self::TYPE_VARCHAR | 16,
		);
	}
}

class SelectTestUserNews extends TableAbstract
{
	public function getConnections()
	{
		return array(
			'userId' => 'psx_sql_table_select_user',
			'newsId' => 'psx_sql_table_select_news',
		);
	}

	public function getName()
	{
		return 'psx_sql_table_select_usernews';
	}

	public function getColumns()
	{
		return array(
			'id'     => self::TYPE_INT | 10 | self::PRIMARY_KEY | self::AUTO_INCREMENT,
			'userId' => self::TYPE_INT | 10,
			'newsId' => self::TYPE_INT | 10,
		);
	}
}

class SelectTestGroup extends TableAbstract
{
	public function getName()
	{
		return 'psx_sql_table_select_group';
	}

	public function getColumns()
	{
		return array(
			'id'   => self::TYPE_INT | 10 | self::PRIMARY_KEY | self::AUTO_INCREMENT,
			'name' => self::TYPE_VARCHAR | 16,
		);
	}
}

class SelectTestRecord
{
	protected $args;

	public function __construct($foo, $bar)
	{
		$this->args = func_get_args();
	}

	public function getArgs()
	{
		return $this->args;
	}
}

