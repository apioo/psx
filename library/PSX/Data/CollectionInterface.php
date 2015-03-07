<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Data;

use Countable;
use Iterator;

/**
 * CollectionInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
