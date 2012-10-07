<?php
/*
 *  $Id: Stmt.php 587 2012-08-15 21:29:29Z k42b3.x@googlemail.com $
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
 * PSX_Sql_Driver_Pdo_Stmt
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Sql
 * @version    $Revision: 587 $
 */
class PSX_Sql_Driver_Pdo_Stmt implements PSX_Sql_StmtInterface
{
	private $handle;
	private $params = array();

	private $isExecuted = false;
	private $length     = 0;

	public function __construct(PDOStatement $stmt)
	{
		$this->handle = $stmt;
	}

	public function bindParam($value)
	{
		array_push($this->params, array(

			'type'  => self::getType($value),
			'value' => $value,

		));
	}

	public function execute()
	{
		$length = count($this->params);

		if($length > 0)
		{
			if($this->isExecuted === false)
			{
				$j = 1;

				foreach($this->params as $i => $param)
				{
					$key = 'var_' . $i;

					$this->$key = null;

					$this->handle->bindParam($j, $this->$key, $param['type']);

					$j++;
				}

				$this->length = $length;

				$this->isExecuted = true;
			}

			if($this->length == $length)
			{
				foreach($this->params as $i => $param)
				{
					$key = 'var_' . $i;

					$this->$key = $param['value'];
				}

				$this->params = array();
			}
			else
			{
				throw new PSX_Sql_Exception('You must provide the same params in a reused stmt');
			}
		}

		$this->handle->execute();
	}

	public function numRows()
	{
		return $this->handle->rowCount();
	}

	public function fetchAssoc()
	{
		$result = array();

		while($row = $this->handle->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $row;
		}

		$this->handle->closeCursor();

		return $result;
	}

	public function fetchObject($class = 'stdClass', array $args = array())
	{
		$result = array();

		while($row = $this->handle->fetchObject($class, $args))
		{
			$result[] = $row;
		}

		$this->handle->closeCursor();

		return $result;
	}

	public function getHandle()
	{
		return $this->handle;
	}

	public function close()
	{
		$this->handle = null;
	}

	public function error()
	{
		$info = $this->handle->errorInfo();

		return isset($info[2]) ? $info[2] : false;
	}

	private static function getType($var)
	{
		if(is_int($var))
		{
			return PDO::PARAM_INT;
		}
		elseif(is_bool($var))
		{
			return PDO::PARAM_BOOL;
		}
		elseif(is_null($var))
		{
			return PDO::PARAM_NULL;
		}
		else
		{
			return PDO::PARAM_STR;
		}
	}
}
