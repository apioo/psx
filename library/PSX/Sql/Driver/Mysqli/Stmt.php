<?php
/*
 *  $Id: Stmt.php 581 2012-08-15 21:26:38Z k42b3.x@googlemail.com $
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
 * PSX_Sql_Driver_Mysqli_Stmt
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Sql
 * @version    $Revision: 581 $
 */
class PSX_Sql_Driver_Mysqli_Stmt implements PSX_Sql_StmtInterface
{
	private $handle;
	private $params = array();

	private $isExecuted = false;
	private $length     = 0;

	public function __construct(mysqli_stmt $stmt)
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
				$data  = array();
				$types = '';

				$data[] =& $types;

				foreach($this->params as $i => $param)
				{
					$key = 'var_' . $i;

					$this->$key = null;

					$data[] =& $this->$key;

					$types.= $param['type'];
				}

				call_user_func_array(array($this->handle, 'bind_param'), $data);

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

		$this->handle->store_result();
	}

	public function numRows()
	{
		return $this->handle->num_rows;
	}

	public function fetchAssoc()
	{
		$meta   = $this->handle->result_metadata();
		$params = array();
		$row    = array();

		while($field = $meta->fetch_field())
		{
			$params[] =& $row[$field->name];
		}

		call_user_func_array(array($this->handle, 'bind_result'), $params);

		while($this->handle->fetch())
		{
			$copy = array();

			foreach($row as $key => $val)
			{
				$copy[$key] = $val;
			}

			$result[] = $copy;
		}

		$this->handle->free_result();

		return $result;
	}

	public function fetchObject($class = 'stdClass', array $args = array())
	{
		$meta = $this->handle->result_metadata();

		while($field = $meta->fetch_field())
		{
			$params[] =& $row[$field->name];
		}

		call_user_func_array(array($this->handle, 'bind_result'), $params);

		$reflection  = new ReflectionClass($class);
		$constructor = $reflection->getConstructor();

		if(empty($constructor))
		{
			while($this->handle->fetch())
			{
				$obj = $reflection->newInstance();

				foreach($row as $key => $val)
				{
					$obj->$key = $val;
				}

				$result[] = $obj;
			}
		}
		else
		{
			while($this->handle->fetch())
			{
				$obj = $reflection->newInstanceArgs($args);

				foreach($row as $key => $val)
				{
					$obj->$key = $val;
				}

				$result[] = $obj;
			}
		}

		$this->handle->free_result();

		return $result;
	}

	public function getHandle()
	{
		return $this->handle;
	}

	public function close()
	{
		$this->handle->close();
	}

	public function error()
	{
		return $this->handle->error;
	}

	private static function getType($var)
	{
		if(is_int($var))
		{
			return 'i';
		}
		elseif(is_double($var))
		{
			return 'd';
		}
		else
		{
			return 's';
		}
	}
}