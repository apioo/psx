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

namespace PSX\Data\Schema\Property;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Data\Schema\PropertyAbstract;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\ValidationException;
use RuntimeException;

/**
 * ComplexType
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ComplexType extends CompositeTypeAbstract
{
	public function validate($data, $path = '/')
	{
		parent::validate($data, $path);

		if($data === null)
		{
			return true;
		}

		if(!$data instanceof \stdClass)
		{
			throw new ValidationException($path . ' must be an object');
		}

		foreach($this->properties as $name => $property)
		{
			$propertyPath = $path === '/' ? '/' : $path . '/';
			$propertyPath.= $property->getName();

			$property->validate(isset($data->$name) ? $data->$name : null, $propertyPath);
		}

		return true;
	}

	public function assimilate($data, $path = '/')
	{
		parent::assimilate($data, $path);

		$data = $this->normalizeToArray($data);

		if(!is_array($data))
		{
			throw new RuntimeException($path . ' must be an object');
		}

		$result = array();
		foreach($this->properties as $name => $property)
		{
			$propertyPath = $path === '/' ? '/' : $path . '/';
			$propertyPath.= $property->getName();

			if(isset($data[$name]))
			{
				$result[$name] = $property->assimilate($data[$name], $propertyPath);
			}
			else if($property->isRequired())
			{
				throw new RuntimeException($propertyPath . ' is required');
			}
		}

		return new Record($this->getName(), $result);
	}

	/**
	 * Returns an value indicating how much the given data structure matches 
	 * this type
	 *
	 * @param mixed $data
	 * @return integer
	 */
	public function match($data)
	{
		$data = $this->normalizeToArray($data);

		if(is_array($data))
		{
			$match = 0;
			foreach($this->properties as $name => $property)
			{
				if(isset($data[$name]))
				{
					$match++;
				}
				else if($property->isRequired())
				{
					return 0;
				}
			}

			return $match / count($this->properties);
		}

		return 0;
	}

	protected function normalizeToArray($data)
	{
		if($data instanceof RecordInterface)
		{
			$data = $data->getRecordInfo()->getData();
		}
		else if($data instanceof \stdClass)
		{
			$data = (array) $data;
		}

		return $data;
	}
}
