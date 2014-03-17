<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Record\Definition;

use PSX\Data\Record\DefinitionInterface;

/**
 * Collection
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Collection implements CollectionInterface
{
	protected $definitions;

	private $_pointer;

	public function __construct()
	{
		$this->definitions = array();
	}

	public function getAll()
	{
		return $this->definitions;
	}

	public function get($name)
	{
		return isset($this->definitions[$name]) ? $this->definitions[$name] : null;
	}

	public function add(DefinitionInterface $definition)
	{
		$this->definitions[$definition->getName()] = $definition;
	}

	public function has($name)
	{
		return isset($this->definitions[$name]);
	}

	public function merge(CollectionInterface $collection)
	{
		$this->definitions = array_merge($this->definitions, $collection->getAll());
	}

	// Iterator
	public function current()
	{
		return current($this->definitions);
	}

	public function key()
	{
		return key($this->definitions);
	}

	public function next()
	{
		return $this->_pointer = next($this->definitions);
	}

	public function rewind()
	{
		$this->_pointer = reset($this->definitions);
	}

	public function valid()
	{
		return $this->_pointer;
	}

	// Countable
	public function count()
	{
		return count($this->definitions);
	}
}
