<?php
/*
 *  $Id: Sql.php 559 2012-07-29 02:39:55Z k42b3.x@googlemail.com $
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
 * PSX_Session_Handler_Sql
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Session
 * @version    $Revision: 559 $
 */
class PSX_Session_Handler_Sql implements PSX_Session_HandlerInterface
{
	protected $sql;
	protected $table;

	public function __construct(PSX_Sql $sql, $table)
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
		$con  = new PSX_Sql_Condition(array('id', '=', $id));
		$data = $this->sql->select($this->table, array('content'), $con, PSX_Sql::SELECT_FIELD);

		return $data;
	}

	public function write($id, $data)
	{
		$this->sql->insert($this->table, array(

			'id'      => $id,
			'content' => $data,
			'date'    => date(PSX_Time::SQL),

		));
	}

	public function delete($id)
	{
		$con = new PSX_Sql_Condition(array('id', '=', $id));

		$this->sql->delete($this->table, $con);
	}

	public function gc($maxTime)
	{
		$con = new PSX_Sql_Condition();
		$con->add('DATE_ADD(date, "INTERVAL ' . $maxTime . ' SECOND")', '<', date(PSX_Time::SQL));

		$this->sql->delete($this->table, $con);

		return true;
	}
}

