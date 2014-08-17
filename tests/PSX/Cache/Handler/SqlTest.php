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

namespace PSX\Cache\Handler;

use PDOException;
use PSX\CacheTest;
use PSX\Sql\Table\ColumnAllocation;

/**
 * SqlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SqlTest extends CacheTest
{
	protected $table = 'psx_cache_handler_sql_test';

	protected function setUp()
	{
		parent::setUp();

		try
		{
			$this->connection = getContainer()->get('connection');
		}
		catch(PDOException $e)
		{
			$this->markTestSkipped($e->getMessage());
		}
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
