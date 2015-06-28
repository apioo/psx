<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Cache\Handler;

use PSX\CacheTest;
use PSX\Sql\Table\ColumnAllocation;
use PSX\Test\Environment;

/**
 * SqlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SqlTest extends CacheTest
{
	protected $table = 'psx_cache_handler_sql_test';

	protected function setUp()
	{
		parent::setUp();

		if(!Environment::hasConnection())
		{
			$this->markTestSkipped('Database connection not available');
		}

		$this->connection = Environment::getService('connection');
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
