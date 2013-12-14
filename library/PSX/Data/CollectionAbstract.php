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

namespace PSX\Data;

use ArrayObject;

/**
 * CollectionAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class CollectionAbstract extends RecordAbstract implements CollectionInterface
{
	protected $collection;

	private $_pointer;

	public function __construct(array $collection = array())
	{
		$this->collection = $collection;
	}

	public function add(RecordInterface $record)
	{
		$this->collection[] = $record;
	}

	public function clear()
	{
		$this->collection = array();
		$this->rewind();
	}

	public function isEmpty()
	{
		return $this->count() == 0;
	}

	public function get($key)
	{
		return $this->collection[$key];
	}

	public function set($key, RecordInterface $record)
	{
		$this->collection[$key] = $record;
	}

	public function toArray()
	{
		return $this->collection;
	}

	// Iterator
	public function current()
	{
		return current($this->collection);
	}

	public function key()
	{
		return key($this->collection);
	}

	public function next()
	{
		return $this->_pointer = next($this->collection);
	}

	public function rewind()
	{
		$this->_pointer = reset($this->collection);
	}

	public function valid()
	{
		return $this->_pointer;
	}

	// Countable
	public function count()
	{
		return count($this->collection);
	}
}
