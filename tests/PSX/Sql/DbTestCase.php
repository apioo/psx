<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Sql;

use PDOException;

/**
 * DbTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class DbTestCase extends \PHPUnit_Extensions_Database_TestCase
{
	protected static $con;

	protected $connection;

	public function getConnection()
	{
		if(!hasConnection())
		{
			$this->markTestSkipped('Database connection not available');
		}

		if(self::$con === null)
		{
			self::$con = getContainer()->get('connection');
		}

		if($this->connection === null)
		{
			$this->connection = self::$con;
		}

		return $this->createDefaultDBConnection($this->connection->getWrappedConnection(), getContainer()->get('config')->get('psx_sql_db'));
	}
}
