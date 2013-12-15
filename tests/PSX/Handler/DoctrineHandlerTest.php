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
use PSX\DateTime;
use PSX\Sql;
use PSX\Sql\Condition;
use PSX\Sql\DbTestCase;
use PSX\Sql\Join;
use PSX\Sql\Table;
use PSX\Sql\TableAbstract;
use PSX\Test\TableDataSet;

/**
 * DoctrineHandlerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DoctrineHandlerTest extends DbTestCase
{
	use HandlerTestCase;

	public function setUp()
	{
		if(!class_exists('Doctrine\ORM\EntityManager'))
		{
			$this->markTestSkipped('Doctrine not installed');
		}

		parent::setUp();
	}

	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(dirname(__FILE__) . '/handler_fixture.xml');
	}

	protected function getHandler()
	{
		return new DoctrineTestHandler(getContainer()->get('entityManager'));
	}
}

class DoctrineTestHandler extends DoctrineHandlerAbstract
{
	public function getEntityName()
	{
		return 'PSX\Handler\DoctrineTestEntity';
	}
}

/**
 * @Entity
 * @Table(name="psx_handler_comment")
 */
class DoctrineTestEntity
{
	/**
	 * @Id
	 * @Column(type="integer", nullable=false)
	 * @GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @Column(type="integer", nullable=false)
	 */
	protected $userId;

	/**
	 * @Column(type="string", nullable=false)
	 */
	protected $title;

	/**
	 * @Column(type="datetime", nullable=false)
	 */
	protected $date;

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setUserId($userId)
	{
		$this->userId = $userId;
	}
	
	public function getUserId()
	{
		return $this->userId;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setDate($date)
	{
		$this->date = $date;
	}
	
	public function getDate()
	{
		return $this->date;
	}
}

