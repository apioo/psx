<?php
/*
 *  $Id: Mysqli.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * Psx_Sql_Driver_Mysqli
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Sql
 * @version    $Revision: 480 $
 */
class PSX_Sql_Driver_Mysqli implements PSX_Sql_DriverInterface
{
	private $handle;
	private $stmt;

	public function connect($host, $user, $pw, $db)
	{
		$this->handle = new mysqli($host, $user, $pw, $db);

		if($this->handle->connect_error)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function exec($sql)
	{
		$result = $this->handle->query($sql);

		if($result === false)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function lastInsertId()
	{
		return $this->handle->insert_id;
	}

	public function close()
	{
		$this->handle->close();
	}

	public function quote($value)
	{
		return '\'' . $this->handle->real_escape_string(strval($value)) . '\'';
	}

	public function error()
	{
		return $this->handle->error;
	}

	public function prepare($sql)
	{
		$stmt = $this->handle->prepare($sql);

		if($stmt === false)
		{
			throw new PSX_Sql_Exception($this->error());
		}
		else
		{
			return new PSX_Sql_Driver_Mysqli_Stmt($stmt);
		}
	}

	public function beginTransaction()
	{
		$this->handle->autocommit(false);
	}

	public function commit()
	{
		$this->handle->commit();
	}

	public function rollback()
	{
		$this->handle->rollback();
	}
}