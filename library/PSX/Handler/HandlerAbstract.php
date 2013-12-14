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

namespace PSX\Handler;

use BadMethodCallException;
use InvalidArgumentException;
use PSX\Sql\Condition;

/**
 * Default abstract class wich implements all necessary methods using an 
 * TableInterface. The TableInterface only simplyfies creating sql queries you
 * could also write an handler wich uses simple sql queries.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class HandlerAbstract implements HandlerInterface
{
	/**
	 * Magic method to make conditional selection
	 *
	 * @param string $method
	 * @param string $arguments
	 * @return mixed
	 */
	public function __call($method, $arguments)
	{
		if(substr($method, 0, 8) == 'getOneBy')
		{
			$column = lcfirst(substr($method, 8));
			$value  = isset($arguments[0]) ? $arguments[0] : null;
			$fields = isset($arguments[1]) ? $arguments[1] : array();
			$mode   = isset($arguments[2]) ? $arguments[2] : 0;
			$class  = isset($arguments[3]) ? $arguments[3] : null;
			$args   = isset($arguments[4]) ? $arguments[4] : array();

			if(!empty($value))
			{
				$con = new Condition(array($column, '=', $value));
			}
			else
			{
				throw new InvalidArgumentException('Value required');
			}

			return $this->getOneBy($con, $fields, $mode, $class, $args);
		}
		else if(substr($method, 0, 5) == 'getBy')
		{
			$column = lcfirst(substr($method, 5));
			$value  = isset($arguments[0]) ? $arguments[0] : null;
			$fields = isset($arguments[1]) ? $arguments[1] : array();
			$mode   = isset($arguments[2]) ? $arguments[2] : 0;
			$class  = isset($arguments[3]) ? $arguments[3] : null;
			$args   = isset($arguments[4]) ? $arguments[4] : array();

			if(!empty($value))
			{
				$con = new Condition(array($column, '=', $value));
			}
			else
			{
				throw new InvalidArgumentException('Value required');
			}

			return $this->getBy($con, $fields, $mode, $class, $args);
		}
		else
		{
			throw new BadMethodCallException('Undefined method');
		}
	}
}
