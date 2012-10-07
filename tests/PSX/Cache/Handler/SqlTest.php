<?php
/*
 *  $Id: SqlTest.php 636 2012-09-01 10:32:42Z k42b3.x@googlemail.com $
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
 * PSX_Cache_Handler_SqlTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 636 $
 */
class PSX_Cache_Handler_SqlTest extends PSX_CacheTest
{
	protected $table;
	protected $sql;

	protected function setUp()
	{
		parent::setUp();

		try
		{
			$config = new PSX_Config('../configuration.php');

			$this->table = __CLASS__;
			$this->sql   = new PSX_Sql($config['psx_sql_host'],
				$config['psx_sql_user'],
				$config['psx_sql_pw'],
				$config['psx_sql_db']);

		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `{$this->table}` (
  `id` VARCHAR(32) NOT NULL,
  `content` BLOB NOT NULL,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8
SQL;

			$this->sql->exec($sql);
			$this->sql->exec('TRUNCATE TABLE ' . $this->table);
		}
		catch(Exception $e)
		{
			$this->markTestSkipped($e->getMessage());
		}
	}

	protected function tearDown()
	{
		if($this->sql instanceof PSX_Sql)
		{
			$this->sql->exec('TRUNCATE TABLE ' . $this->table);
		}

		unset($this->table);
		unset($this->sql);
	}

	protected function getHandler()
	{
		return new PSX_Cache_Handler_Sql(new PSX_Cache_Handler_Sql_TableAbstract_Test($this->sql));
	}
}

class PSX_Cache_Handler_Sql_TableAbstract_Test extends PSX_Cache_Handler_Sql_TableAbstract
{
	public function getName()
	{
		return 'PSX_Cache_Handler_SqlTest';
	}
}

