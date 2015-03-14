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

namespace PSX\Data\Schema;

use InvalidArgumentException;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;

/**
 * Assimilator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Assimilator
{
	/**
	 * Takes an array and fits it accoring to the specification. Removes all 
	 * unknown keys. If an value doesnt fit or an required parameter is missing 
	 * it throws an exception
	 *
	 * @param array $data
	 * @param PSX\Data\SchemaInterface $schema
	 * @return array
	 */
	public function assimilate(SchemaInterface $schema, $data)
	{
		return $this->recAssimilate($schema->getDefinition(), $data);
	}

	protected function recAssimilate(PropertyInterface $type, $data)
	{
		if($type instanceof Property\ComplexType)
		{
			if($data instanceof RecordInterface)
			{
				$data = $data->getRecordInfo()->getData();
			}
			else if($data instanceof \stdClass)
			{
				$data = (array) $data;
			}

			if(!is_array($data))
			{
				throw new InvalidArgumentException('Value of ' . $type->getName() . ' must be an object');
			}

			$properties = $type->getProperties();
			$result     = array();

			foreach($properties as $name => $property)
			{
				if(isset($data[$name]))
				{
					$result[$name] = $this->recAssimilate($property, $data[$name]);
				}
				else if($property->isRequired())
				{
					throw new InvalidArgumentException('Required parameter ' . $property->getName() . ' is missing');
				}
			}

			return new Record($type->getName(), $result);
		}
		else if($type instanceof Property\ArrayType)
		{
			if(!is_array($data))
			{
				throw new InvalidArgumentException('Value of ' . $type->getName() . ' must be an array');
			}

			$prototype = $type->getPrototype();
			$result    = array();

			foreach($data as $value)
			{
				$result[] = $this->recAssimilate($prototype, $value);
			}

			return $result;
		}
		else if($type instanceof Property\Integer)
		{
			return (int) $data;
		}
		else if($type instanceof Property\Float)
		{
			return (float) $data;
		}
		else if($type instanceof Property\Boolean)
		{
			return (bool) $data;
		}
		else if($type instanceof Property\DateTime || $type instanceof Property\Date)
		{
			return $data instanceof \DateTime ? $data : new \DateTime($data);
		}
		else
		{
			return (string) $data;
		}
	}
}
