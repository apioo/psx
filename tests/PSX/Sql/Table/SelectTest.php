<?php
/*
 *  $Id: SelectTest.php 596 2012-08-15 22:24:44Z k42b3.x@googlemail.com $
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

namespace PSX\Sql\Table;

use PSX\Data\ResultSet;
use PSX\DateTime;
use PSX\Sql;
use PSX\Sql\DbTestCase;
use PSX\Sql\Join;
use PSX\Sql\TableAbstract;
use PSX\Test\TableDataSet;

/**
 * PSX_Sql_Table_SelectTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 596 $
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
			array('id' => null, 'name' => 'foo'),
			array('id' => null, 'name' => 'bar'),
		));

		$dataSet->addTable(new SelectTestUserNews($this->sql), array(
			array('id' => null, 'userId' => 1, 'newsId' => 1),
			array('id' => null, 'userId' => 1, 'newsId' => 2),
			array('id' => null, 'userId' => 1, 'newsId' => 3),
			array('id' => null, 'userId' => 1, 'newsId' => 4),
			array('id' => null, 'userId' => 2, 'newsId' => 1),
			array('id' => null, 'userId' => 2, 'newsId' => 2),
		));

		return $dataSet;
	}

	public function testJoinType()
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
	}

	public function testJoinCardinality()
	{
		$news     = new SelectTestNews($this->sql);
		$user     = new SelectTestUser($this->sql);
		$userNews = new SelectTestUserNews($this->sql);

		$result = $userNews->select(array('userId', 'newsId'))
			->join(Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->join(Join::INNER, $news
				->select(array('id', 'title'), 'news')
			)
			->getAll();

		$this->assertEquals(6, count($result));


		$news     = new SelectTestNews($this->sql);
		$userNews = new SelectTestUserNews($this->sql);

		$result = $news->select(array('id', 'title'))
			->join(Join::INNER, $userNews
				->select(array('userId', 'newsId'), 'foo')
			, '1:n')
			->getAll();

		$this->assertEquals(6, count($result));
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
		$this->assertEquals(true, isset($row['id']));
		$this->assertEquals(true, isset($row['userId']));
		$this->assertEquals(true, isset($row['title']));
		$this->assertEquals(true, isset($row['clientId']));
		$this->assertEquals(true, isset($row['clientName']));

		// object
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

		$this->assertEquals(true, $row instanceof \stdClass);
		$this->assertEquals(true, isset($row->id));
		$this->assertEquals(true, isset($row->userId));
		$this->assertEquals(true, isset($row->title));
		$this->assertEquals(true, isset($row->clientId));
		$this->assertEquals(true, isset($row->clientName));
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
		$this->assertEquals(true, isset($row['id']));
		$this->assertEquals(true, isset($row['userId']));
		$this->assertEquals(true, isset($row['title']));
		$this->assertEquals(true, isset($row['clientId']));
		$this->assertEquals(true, isset($row['clientName']));

		// object
		$news = new SelectTestNews($this->sql);
		$user = new SelectTestUser($this->sql);
		$row  = $news->select(array('id', 'userId', 'title'))
			->join(Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->limit(1)
			->getRow(Sql::FETCH_OBJECT);

		$this->assertEquals(true, $row instanceof \stdClass);
		$this->assertEquals(true, isset($row->id));
		$this->assertEquals(true, isset($row->userId));
		$this->assertEquals(true, isset($row->title));
		$this->assertEquals(true, isset($row->clientId));
		$this->assertEquals(true, isset($row->clientName));
	}

	public function testGetResultSet()
	{
		$news   = new SelectTestNews($this->sql);
		$result = $news->select(array('id', 'userId', 'title'))
			->getResultSet(0, 16, 'id', 'ascending');

		$this->assertEquals(true, $result instanceof ResultSet);
		$this->assertEquals(4, $result->getLength());
		$this->assertEquals(4, count($result->entry));

		reset($result);
		$len = count($result);

		for($i = 1; $i <= $len; $i++)
		{
			$entry = $result->current();

			$this->assertEquals(true, isset($entry['id']));
			$this->assertEquals(true, isset($entry['title']));
			$this->assertEquals($i, $entry['id']);

			$result->next();
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

	public function getDefaultRecordClass()
	{
		return 'stdClass';
	}

	public function getDefaultRecordArgs()
	{
		return array();
	}
}

class SelectTestUser extends TableAbstract
{
	public function getConnections()
	{
		return array();
	}

	public function getName()
	{
		return 'psx_sql_table_select_user';
	}

	public function getColumns()
	{
		return array(
			'id'   => self::TYPE_INT | 10 | self::PRIMARY_KEY | self::AUTO_INCREMENT,
			'name' => self::TYPE_VARCHAR | 16,
		);
	}

	public function getDefaultRecordClass()
	{
		return 'stdClass';
	}

	public function getDefaultRecordArgs()
	{
		return array();
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

	public function getDefaultRecordClass()
	{
		return 'stdClass';
	}

	public function getDefaultRecordArgs()
	{
		return array();
	}
}
