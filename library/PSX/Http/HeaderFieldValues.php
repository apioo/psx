<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Http;

use DateTime;

/**
 * HeaderFieldValues
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class HeaderFieldValues implements \Countable, \Iterator, \ArrayAccess
{
	protected $value;

	protected $_pointer;

	public function __construct($value)
	{
		$this->value = array();

		$this->append($value);
	}

	public function getValue($asArray = false)
	{
		return $asArray ? $this->value : implode(', ', $this->value);
	}

	public function append($value)
	{
		if($value instanceof HeaderFieldValues)
		{
			$value = $value->getValue(true);
		}

		if(is_array($value))
		{
			$this->value = array_merge($this->value, array_map('strval', $value));
		}
		else if($value instanceof DateTime)
		{
			$this->value[] = $value->format(\PSX\DateTime::HTTP);
		}
		else
		{
			$this->value[] = (string) $value;
		}
	}

	public function __toString()
	{
		return implode(', ', $this->value);
	}

	// Countable
	public function count()
	{
		return count($this->value);
	}

	// Traversable
	public function current()
	{
		return current($this->value);
	}

	public function key()
	{
		return key($this->value);
	}

	public function next()
	{
		return $this->_pointer = next($this->value);
	}

	public function rewind()
	{
		$this->_pointer = reset($this->value);
	}

	public function valid()
	{
		return $this->_pointer;
	}

	// ArrayAccess
	public function offsetExists($key)
	{
		return isset($this->value[$key]);
	}

	public function offsetGet($key)
	{
		return isset($this->value[$key]) ? $this->value[$key] : null;
	}

	public function offsetSet($key, $value)
	{
		if(isset($this->value[$key]))
		{
			$this->value[$key] = $value;
		}
	}

	public function offsetUnset($key)
	{
		if(isset($this->value[$key]))
		{
			unset($this->value[$key]);
		}
	}
}
