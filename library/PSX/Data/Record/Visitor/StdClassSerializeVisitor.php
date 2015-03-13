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
use PSX\Data\Record\VisitorAbstract;

/**
 * This visitor creates a new object tree using an simple stdClass for object 
 * representation instead of an RecordInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StdClassSerializeVisitor extends VisitorAbstract
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

	public function visitObjectStart(RecordInterface $record)
	{
		$this->objectStack[] = new \stdClass();

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

		$this->objectStack[$this->objectCount]->$key = $this->getValue($value);
	}

	public function visitArrayStart(array $array)
	{
		$this->arrayStack[] = array();

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

		$this->arrayStack[$this->arrayCount][] = $this->getValue($value);
	}

	protected function getValue($value)
	{
		if($value instanceof RecordInterface)
		{
			return $this->lastObject;
		}
		else if($value instanceof \DateTime)
		{
			return $value->format(\DateTime::RFC3339);
		}
		else if(is_object($value))
		{
			return (string) $value;
		}
		else if(is_array($value))
		{
			return $this->lastArray;
		}
		else
		{
			return $value;
		}
	}
}
