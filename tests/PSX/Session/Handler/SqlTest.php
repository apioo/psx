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

namespace PSX\Session\Handler;

use PDOException;
use PSX\SessionTest;

/**
 * SqlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SqlTest extends SessionTest
{
	protected $table = 'psx_session_handler_sql_test';
	protected $sess;

	protected function setUp()
	{
		try
		{
			$this->sql = getContainer()->get('sql');

			$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `{$this->table}` (
  `id` VARCHAR(32) NOT NULL,
  `content` BLOB NOT NULL,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8
SQL;

			$this->sql->exec($sql);
		}
		catch(PDOException $e)
		{
			$this->markTestSkipped($e->getMessage());
		}

		parent::setUp();
	}

	protected function getHandler()
	{
		return new Sql($this->sql, $this->table);
	}
}