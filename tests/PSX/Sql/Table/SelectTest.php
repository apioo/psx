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

/**
 * PSX_Sql_Table_SelectTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 596 $
 */
class PSX_Sql_Table_SelectTest extends PHPUnit_Framework_TestCase
{
	private $sql;
	private $tableNews;
	private $tableUser;
	private $tableUserNews;

	private $dataNews = array(
		array('userId' => 1, 'title' => 'foo'),
		array('userId' => 1, 'title' => 'bar'),
		array('userId' => 2, 'title' => 'test'),
		array('userId' => 3, 'title' => 'blub'),
	);

	private $dataUser = array(
		array('name' => 'foo'),
		array('name' => 'bar'),
	);

	private $dataUserNews = array(
		array('userId' => 1, 'newsId' => 1),
		array('userId' => 1, 'newsId' => 2),
		array('userId' => 1, 'newsId' => 3),
		array('userId' => 1, 'newsId' => 4),
		array('userId' => 2, 'newsId' => 1),
		array('userId' => 2, 'newsId' => 2),
	);

	protected function setUp()
	{
		try
		{
			$config = getConfig();

			$this->sql = new PSX_Sql($config['psx_sql_host'],
				$config['psx_sql_user'],
				$config['psx_sql_pw'],
				$config['psx_sql_db']);

			$this->setUpNews();
			$this->setUpUser();
			$this->setUpUserNews();
		}
		catch(Exception $e)
		{
			$this->markTestSkipped($e->getMessage());
		}
	}

	private function setUpNews()
	{
		$this->tableNews = new PSX_Sql_Table_SelectTest_News($this->sql);

		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `{$this->tableNews->getName()}` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userId` int(10) NOT NULL,
  `title` varchar(32) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

		$this->sql->exec($sql);

		$this->sql->exec('TRUNCATE TABLE ' . $this->tableNews->getName());

		foreach($this->dataNews as $row)
		{
			$this->tableNews->insert(array(
				'userId' => $row['userId'],
				'title'  => $row['title'],
				'date'   => date(PSX_DateTime::SQL),
			));
		}
	}

	private function setUpUser()
	{
		$this->tableUser = new PSX_Sql_Table_SelectTest_User($this->sql);

		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `{$this->tableUser->getName()}` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

		$this->sql->exec($sql);

		$this->sql->exec('TRUNCATE TABLE ' . $this->tableUser->getName());

		foreach($this->dataUser as $row)
		{
			$this->tableUser->insert(array(
				'name' => $row['name'],
			));
		}
	}

	private function setUpUserNews()
	{
		$this->tableUserNews = new PSX_Sql_Table_SelectTest_UserNews($this->sql);

		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `{$this->tableUserNews->getName()}` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userId` int(10) NOT NULL,
  `newsId` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

		$this->sql->exec($sql);

		$this->sql->exec('TRUNCATE TABLE ' . $this->tableUserNews->getName());

		foreach($this->dataUserNews as $row)
		{
			$this->tableUserNews->insert(array(
				'userId' => $row['userId'],
				'newsId' => $row['newsId'],
			));
		}
	}

	protected function tearDown()
	{
		if($this->sql instanceof PSX_Sql)
		{
			$this->sql->exec('TRUNCATE TABLE ' . $this->tableNews->getName());

			$this->sql->exec('TRUNCATE TABLE ' . $this->tableUser->getName());
		}

		unset($this->tableUser);
		unset($this->tableNews);
		unset($this->sql);
	}

	public function testJoinType()
	{
		// inner
		$news   = new PSX_Sql_Table_SelectTest_News($this->sql);
		$user   = new PSX_Sql_Table_SelectTest_User($this->sql);
		$result = $news->select(array('id', 'userId', 'title'))
			->join(PSX_Sql_Join::INNER, $user
				->select(array('id', 'name'), 'user')
			)
			->getAll();

		$this->assertEquals(3, count($result));

		// left
		$news   = new PSX_Sql_Table_SelectTest_News($this->sql);
		$user   = new PSX_Sql_Table_SelectTest_User($this->sql);
		$result = $news->select(array('id', 'userId', 'title'))
			->join(PSX_Sql_Join::LEFT, $user
				->select(array('id', 'name'), 'user')
			)
			->getAll();

		$this->assertEquals(4, count($result));
	}

	public function testJoinCardinality()
	{
		$news     = new PSX_Sql_Table_SelectTest_News($this->sql);
		$user     = new PSX_Sql_Table_SelectTest_User($this->sql);
		$userNews = new PSX_Sql_Table_SelectTest_UserNews($this->sql);

		$result = $userNews->select(array('userId', 'newsId'))
			->join(PSX_Sql_Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->join(PSX_Sql_Join::INNER, $news
				->select(array('id', 'title'), 'news')
			)
			->getAll();

		$this->assertEquals(6, count($result));


		$news     = new PSX_Sql_Table_SelectTest_News($this->sql);
		$userNews = new PSX_Sql_Table_SelectTest_UserNews($this->sql);

		$result = $news->select(array('id', 'title'))
			->join(PSX_Sql_Join::INNER, $userNews
				->select(array('userId', 'newsId'), 'foo')
			, '1:n')
			->getAll();

		$this->assertEquals(6, count($result));
	}

	public function testGetAll()
	{
		// array
		$news   = new PSX_Sql_Table_SelectTest_News($this->sql);
		$user   = new PSX_Sql_Table_SelectTest_User($this->sql);
		$result = $news->select(array('id', 'userId', 'title'))
			->join(PSX_Sql_Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->orderBy('clientId', PSX_Sql::SORT_DESC)
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
		$news   = new PSX_Sql_Table_SelectTest_News($this->sql);
		$user   = new PSX_Sql_Table_SelectTest_User($this->sql);
		$result = $news->select(array('id', 'userId', 'title'))
			->join(PSX_Sql_Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->orderBy('id', PSX_Sql::SORT_DESC)
			->limit(1)
			->getAll(PSX_Sql::FETCH_OBJECT);

		$row = current($result);

		$this->assertEquals(true, $row instanceof stdClass);
		$this->assertEquals(true, isset($row->id));
		$this->assertEquals(true, isset($row->userId));
		$this->assertEquals(true, isset($row->title));
		$this->assertEquals(true, isset($row->clientId));
		$this->assertEquals(true, isset($row->clientName));
	}

	public function testGetRow()
	{
		// array
		$news = new PSX_Sql_Table_SelectTest_News($this->sql);
		$user = new PSX_Sql_Table_SelectTest_User($this->sql);
		$row  = $news->select(array('id', 'userId', 'title'))
			->join(PSX_Sql_Join::INNER, $user
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
		$news = new PSX_Sql_Table_SelectTest_News($this->sql);
		$user = new PSX_Sql_Table_SelectTest_User($this->sql);
		$row  = $news->select(array('id', 'userId', 'title'))
			->join(PSX_Sql_Join::INNER, $user
				->select(array('id', 'name'), 'client')
			)
			->limit(1)
			->getRow(PSX_Sql::FETCH_OBJECT);

		$this->assertEquals(true, $row instanceof stdClass);
		$this->assertEquals(true, isset($row->id));
		$this->assertEquals(true, isset($row->userId));
		$this->assertEquals(true, isset($row->title));
		$this->assertEquals(true, isset($row->clientId));
		$this->assertEquals(true, isset($row->clientName));
	}

	public function testGetResultSet()
	{
		$news   = new PSX_Sql_Table_SelectTest_News($this->sql);
		$result = $news->select(array('id', 'userId', 'title'))
			->getResultSet(0, 16, 'id', 'ascending');

		$this->assertEquals(true, $result instanceof PSX_Data_ResultSet);
		$this->assertEquals(4, $result->getLength());
		$this->assertEquals(4, count($result->entry));

		$i = 1;

		foreach($this->dataNews as $row)
		{
			$entry = $result->current();

			$this->assertEquals(true, isset($entry['id']));
			$this->assertEquals(true, isset($entry['title']));
			$this->assertEquals($i, $entry['id']);
			$this->assertEquals($row['userId'], $entry['userId']);
			$this->assertEquals($row['title'], $entry['title']);

			$result->next();
			$i++;
		}
	}
}

class PSX_Sql_Table_SelectTest_News extends PSX_Sql_TableAbstract
{
	public function getConnections()
	{
		return array(
			'userId' => 'PSX_Sql_Table_SelectTest_User',
		);
	}

	public function getName()
	{
		return __CLASS__;
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

class PSX_Sql_Table_SelectTest_User extends PSX_Sql_TableAbstract
{
	public function getConnections()
	{
		return array();
	}

	public function getName()
	{
		return __CLASS__;
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

class PSX_Sql_Table_SelectTest_UserNews extends PSX_Sql_TableAbstract
{
	public function getConnections()
	{
		return array(
			'userId' => 'PSX_Sql_Table_SelectTest_User',
			'newsId' => 'PSX_Sql_Table_SelectTest_News',
		);
	}

	public function getName()
	{
		return __CLASS__;
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