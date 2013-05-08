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

use PSX\DateTime;
use PSX\Session\HandlerInterface;
use PSX\Sql as SqlDriver;
use PSX\Sql\Condition;

/**
 * Sql
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Sql implements HandlerInterface
{
	protected $sql;
	protected $table;

	public function __construct(SqlDriver $sql, $table)
	{
		$this->sql   = $sql;
		$this->table = $table;
	}

	public function open($path, $name)
	{
		return true;
	}

	public function close()
	{
		return true;
	}

	public function read($id)
	{
		return $this->sql->getField('SELECT `content` FROM `' . $this->table . '` WHERE `id` = ?', array($id));
	}

	public function write($id, $data)
	{
		$this->sql->insert($this->table, array(

			'id'      => $id,
			'content' => $data,
			'date'    => date(DateTime::SQL),

		));
	}

	public function delete($id)
	{
		$con = new Condition(array('id', '=', $id));

		$this->sql->delete($this->table, $con);
	}

	public function gc($maxTime)
	{
		$con = new Condition();
		$con->add('DATE_ADD(`date`, "INTERVAL ' . $maxTime . ' SECOND")', '<', date(DateTime::SQL));

		$this->sql->delete($this->table, $con);

		return true;
	}
}

