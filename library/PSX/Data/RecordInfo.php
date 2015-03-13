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

use ArrayIterator;
use IteratorAggregate;

/**
 * RecordInfo
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RecordInfo implements IteratorAggregate
{
	protected $name;
	protected $fields;

	public function __construct($name, array $fields, RecordInfo $parent = null)
	{
		$this->name = $name;

		if($parent !== null)
		{
			$this->fields = array_merge($parent->getFields(), $fields);
		}
		else
		{
			$this->fields = $fields;
		}
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setFields(array $fields)
	{
		$this->fields = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function hasField($key)
	{
		return isset($this->fields[$key]);
	}

	public function getField($key)
	{
		return isset($this->fields[$key]) ? $this->fields[$key] : null;
	}

	public function isEmpty()
	{
		return empty($this->fields);
	}

	/**
	 * Returns all fields wich are set
	 *
	 * @return array
	 */
	public function getData()
	{
		$data = array();

		foreach($this->fields as $key => $value)
		{
			if(isset($value))
			{
				$data[$key] = $value;
			}
		}

		return $data;
	}

	public function getIterator()
	{
		return new ArrayIterator($this->getData());
	}
}
