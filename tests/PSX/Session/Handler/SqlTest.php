<?php
/*
 *  $Id: SqlTest.php 596 2012-08-15 22:24:44Z k42b3.x@googlemail.com $
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
 * PSX_Session_Handler_SqlTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 596 $
 */
class PSX_Session_Handler_SqlTest extends PSX_SessionTest
{
	protected $table;
	protected $sql;

	protected function setUp()
	{
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

		parent::setUp();
	}

	protected function tearDown()
	{
		parent::tearDown();

		if($this->sql instanceof PSX_Sql)
		{
			$this->sql->exec('TRUNCATE TABLE ' . $this->table);
		}

		unset($this->table);
		unset($this->sql);
	}

	protected function getHandler()
	{
		return new PSX_Session_Handler_Sql($this->sql, $this->table);
	}
}