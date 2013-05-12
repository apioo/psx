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

namespace PSX\Cache\Handler;

use PDOException;
use PSX\Sql AS SqlDriver;
use PSX\Sql\DbTestCase;
use PSX\Sql\TableAbstract;
use PSX\Sql\TableInterface;
use PSX\CacheTest;
use PSX\Exception;

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
			$config = getConfig();

			$this->sql = new SqlDriver($config['psx_sql_host'],
				$config['psx_sql_user'],
				$config['psx_sql_pw'],
				$config['psx_sql_db']);
		}
		catch(PDOException $e)
		{
			$this->markTestSkipped($e->getMessage());
		}
	}

	protected function getHandler()
	{
		return new Sql(new TableCacheTest($this->sql));
	}
}

class TableCacheTest extends TableAbstract
{
	public function getName()
	{
		return 'psx_cache_handler_sql_test';
	}

	public function getColumns()
	{
		return array(
			'id'      => TableInterface::TYPE_VARCHAR | TableInterface::PRIMARY_KEY,
			'content' => TableInterface::TYPE_BLOB,
			'date'    => TableInterface::TYPE_DATETIME,
		);
	}
}

