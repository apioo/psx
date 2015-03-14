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

namespace PSX\Data\Record\Visitor;

use PSX\Data\RecordInterface;
use PSX\Data\Record\GraphTraverser;
use PSX\Data\Record\VisitorAbstract;
use RuntimeException;
use XMLWriter;

/**
 * SerializeVisitorAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class SerializeVisitorAbstract extends VisitorAbstract
{
	protected $objectStack = array();
	protected $objectCount = -1;

	protected $arrayStack  = array();
	protected $arrayCount  = -1;

	protected $lastObject;
	protected $lastArray;

	protected $stack = array();

	public function getObject()
	{
		return $this->lastObject;
	}

	public function visitObjectStart($name)
	{
		$this->objectStack[] = $this->newObject();

		$this->objectCount++;
	}

	public function visitObjectEnd()
	{
		$this->lastObject = array_pop($this->objectStack);

		$this->objectCount--;
	}

	public function visitObjectValueStart($key, $value)
	{
		$this->stack[] = [$key, $value];
	}

	public function visitObjectValueEnd()
	{
		list($key, $value) = array_pop($this->stack);

		$this->addObjectValue($key, $this->getValue($value), $this->objectStack[$this->objectCount]);
	}

	public function visitArrayStart()
	{
		$this->arrayStack[] = $this->newArray();

		$this->arrayCount++;
	}

	public function visitArrayEnd()
	{
		$this->lastArray = array_pop($this->arrayStack);

		$this->arrayCount--;
	}

	public function visitArrayValueStart($value)
	{
		$this->stack[] = [$value];
	}

	public function visitArrayValueEnd()
	{
		list($value) = array_pop($this->stack);

		$this->addArrayValue($this->getValue($value), $this->arrayStack[$this->arrayCount]);
	}

	/**
	 * Returns an new object instance
	 *
	 * @return mixed
	 */
	abstract protected function newObject();

	/**
	 * Adds an key value pair to the object
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $object
	 */
	abstract protected function addObjectValue($key, $value, &$object);

	/**
	 * Returns an new array instance
	 *
	 * @return mixed
	 */
	abstract protected function newArray();

	/**
	 * Adds an value to an array
	 *
	 * @param mixed $value
	 * @param mixed $array
	 */
	abstract protected function addArrayValue($value, &$array);

	protected function newValue($value)
	{
		if($value instanceof \DateTime)
		{
			return $value->format(\DateTime::RFC3339);
		}
		else if(is_scalar($value))
		{
			return $value;
		}
		else
		{
			return (string) $value;
		}
	}

	protected function getValue($value)
	{
		if(GraphTraverser::isObject($value))
		{
			return $this->lastObject;
		}
		else if(GraphTraverser::isArray($value))
		{
			return $this->lastArray;
		}
		else
		{
			return $this->newValue($value);
		}
	}
}
