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

namespace PSX\Session\Handler;

use PDOException;
use PSX\Sql\Table;
use PSX\Sql\DbTestCase;
use PSX\Sql\TableInterface;
use PSX\Sql\Table\ColumnAllocation;

/**
 * SqlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SqlTest extends DbTestCase
{
	protected $table = 'psx_session_handler_sql_test';

	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(__DIR__ . '/handler_fixture.xml');
	}

	public function testOpen()
	{
		$this->getHandler()->open('/', 'file');
	}

	public function testClose()
	{
		$this->getHandler()->close();		
	}

	public function testRead()
	{
		$data = $this->getHandler()->read('0bb3df120bff3c64e9ef553b61ffcd06');

		$this->assertEquals('foobar', $data);

		$data = $this->getHandler()->read('unknown');

		$this->assertEquals(null, $data);
	}

	public function testWrite()
	{
		$this->getHandler()->write('eae84a980e3bcd9bb04cb866facc9385', 'foobar2');

		$this->assertEquals('foobar2', $this->getHandler()->read('eae84a980e3bcd9bb04cb866facc9385'));
	}

	public function testDestroy()
	{
		$data = $this->getHandler()->read('0bb3df120bff3c64e9ef553b61ffcd06');

		$this->assertEquals('foobar', $data);

		$this->getHandler()->destroy('0bb3df120bff3c64e9ef553b61ffcd06');

		$data = $this->getHandler()->read('0bb3df120bff3c64e9ef553b61ffcd06');

		$this->assertEquals(null, $data);
	}

	public function testGc()
	{
		$data = $this->getHandler()->read('0bb3df120bff3c64e9ef553b61ffcd06');

		$this->assertEquals('foobar', $data);

		$this->getHandler()->gc(1);

		$data = $this->getHandler()->read('0bb3df120bff3c64e9ef553b61ffcd06');

		$this->assertEquals(null, $data);
	}

	protected function getHandler()
	{
		$allocation = new ColumnAllocation(array(
			Sql::COLUMN_ID      => 'id',
			Sql::COLUMN_CONTENT => 'content',
			Sql::COLUMN_DATE    => 'date',
		));

		return new Sql($this->connection, $this->table, $allocation);
	}
}