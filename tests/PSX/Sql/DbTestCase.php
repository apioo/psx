<?php
/*
 *  $Id: TableTest.php 639 2012-09-30 22:43:50Z k42b3.x@googlemail.com $
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

namespace PSX\Sql;

use PDOException;
use PSX\Config;
use PSX\Data\Record;
use PSX\DateTime;
use PSX\Sql;
use PSX\Sql\Table\Select;

/**
 * PSX_Sql_TableTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 639 $
 */
abstract class DbTestCase extends \PHPUnit_Extensions_Database_TestCase
{
	protected static $con;

	protected $sql;

	public function getConnection()
	{
		$config = getConfig();

		if(self::$con === null)
		{
			try
			{
				self::$con = new Sql($config['psx_sql_host'],
					$config['psx_sql_user'],
					$config['psx_sql_pw'],
					$config['psx_sql_db']);
			}
			catch(PDOException $e)
			{
				$this->markTestSkipped($e->getMessage());
			}

			// create tables
			$queries = $this->getBeforeQueries();

			foreach($queries as $query)
			{
				self::$con->exec($query);
			}
		}

		$this->sql = self::$con;

		return $this->createDefaultDBConnection($this->sql, $config['psx_sql_db']);
	}

	public function getBeforeQueries()
	{
		return array();
	}
}
