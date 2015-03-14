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

namespace PSX\Data\Record;

use PSX\Data\RecordInterface;

/**
 * VisitorInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface VisitorInterface
{
	/**
	 * Visited if an object begins
	 *
	 * @param string $name
	 */
	public function visitObjectStart($name);

	/**
	 * Visited if an object ends
	 */
	public function visitObjectEnd();

	/**
	 * Visited for each object key value pair
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function visitObjectValueStart($key, $value);

	/**
	 * Visited if an object value ends
	 */
	public function visitObjectValueEnd();

	/**
	 * Visited if an array begins
	 *
	 * @param array $array
	 */
	public function visitArrayStart();

	/**
	 * Visited if an array ends
	 */
	public function visitArrayEnd();

	/**
	 * Visited for each array value
	 *
	 * @param mixed $value
	 */
	public function visitArrayValueStart($value);

	/**
	 * Visited if an array value ends
	 */
	public function visitArrayValueEnd();

	/**
	 * Visited for each value in the tree which is not an object or array
	 */
	public function visitValue($value);
}
