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

use Countable;
use Iterator;
use PSX\Data\Record\DefinitionInterface;

/**
 * Represents a collection of record definitions
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface CollectionInterface
{
	/**
	 * Returns all record definitions
	 *
	 * @return array<PSX\Data\Record\DefinitionInterface>
	 */
	public function getAll();

	/**
	 * Returns the record definition for the specific name
	 *
	 * @param string $name
	 * @return PSX\Data\Record\DefinitionInterface
	 */
	public function get($name);

	/**
	 * Adds an record definition to the collection
	 *
	 * @param PSX\Data\Record\DefinitionInterface $definition
	 */
	public function add(DefinitionInterface $definition);

	/**
	 * Returns whether the record definition is available on this collection
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function has($name);

	/**
	 * Merges another collection into this
	 *
	 * @param PSX\Data\Record\Definition\CollectionInterface $collection
	 */
	public function merge(CollectionInterface $collection);
}
