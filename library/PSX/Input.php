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

namespace PSX;

use PSX\Input\ContainerInterface;

/**
 * This class is for handling data wich came from untrusted sources i.e. GET or
 * POST values.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Input implements ContainerInterface
{
	protected $container;
	protected $validate;

	public function __construct(array &$container, Validate $validate = null)
	{
		$this->container =& $container;
		$this->validate  = $validate === null ? new Validate() : $validate;
	}

	public function getContainer()
	{
		return $this->container;
	}

	public function getValidator()
	{
		return $this->validate;
	}

	/**
	 * Wrapper to the get() method
	 *
	 * @param string $key
	 * @return false|string
	 */
	public function __get($key)
	{
		return $this->offsetGet($key);
	}

	/**
	 * Returns the value $key and applies all filters on the value
	 *
	 * @param string $key
	 * @param array $parameters
	 * @return false|integer|float|string|boolean
	 */
	public function __call($key, $parameters)
	{
		$value    = $this->offsetGet($key);
		$type     = isset($parameters[0]) ? $parameters[0] : Validate::TYPE_STRING;
		$filter   = isset($parameters[1]) ? $parameters[1] : array();
		$key      = isset($parameters[2]) ? $parameters[2] : $key;
		$title    = isset($parameters[3]) ? $parameters[3] : $key;
		$required = isset($parameters[4]) ? $parameters[4] : true;
		$return   = isset($parameters[5]) ? $parameters[5] : false;

		return $this->validate->apply($value, $type, $filter, $key, $title, $required, $return);
	}

	// Countable
	public function count()
	{
		return count($this->container);
	}

	// ArrayAccess
	public function offsetExists($offset)
	{
		return isset($this->container[$offset]);
	}

	public function offsetGet($offset)
	{
		return isset($this->container[$offset]) ? $this->container[$offset] : false;
	}

	public function offsetSet($offset, $value)
	{
		$this->container[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		if(isset($this->container[$offset]))
		{
			unset($this->container[$offset]);
		}
	}
}

