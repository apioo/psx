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

namespace PSX\Data;

use Countable;
use Iterator;

/**
 * CollectionInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface CollectionInterface extends RecordInterface, Iterator, Countable
{
	/**
	 * Adds an record to the collection
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	public function add(RecordInterface $record);

	/**
	 * Clears all entries from the collection
	 *
	 * @return void
	 */
	public function clear();

	/**
	 * Returns whether the collection is empty
	 *
	 * @return boolean
	 */
	public function isEmpty();

	/**
	 * Returns the record with the specific key or null
	 *
	 * @param string $key
	 * @return PSX\Data\RecordInterface
	 */
	public function get($key);

	/**
	 * Sets the record for the specific field
	 *
	 * @param string $key
	 * @param PSX\Data\RecordInterface $record
	 */
	public function set($key, RecordInterface $record);

	/**
	 * Returns an array containing all record of the collection
	 *
	 * @return array<PSX\Data\RecordInterface>
	 */
	public function toArray();
}
