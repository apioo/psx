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

use PSX\Data\RecordInterface;
use PSX\Data\SchemaInterface;
use Traversable;

/**
 * SchemaTraverser
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SchemaTraverser
{
	const TYPE_INCOMING = 0x1;
	const TYPE_OUTGOING = 0x2;

	protected $pathStack = array();

	/**
	 * Traverses through the data based on the provided schema and calls the 
	 * visitor methods. The type tells whether we going through incoming or 
	 * outgoing data. The traverser is for incoming data stricter then for 
	 * outgoing data
	 *
	 * @param mixed $data
	 * @param PSX\Data\SchemaInterface $schema
	 * @param PSX\Data\Schema\VisitorInterface $visitor
	 * @param integer $type
	 * @return mixed
	 */
	public function traverse($data, SchemaInterface $schema, VisitorInterface $visitor, $type)
	{
		$this->pathStack = array();

		return $this->recTraverse($data, $schema->getDefinition(), $visitor, $type);
	}

	protected function recTraverse($data, PropertyInterface $property, VisitorInterface $visitor, $type)
	{
		if($property instanceof Property\ArrayType)
		{
			if(!is_array($data) && !$data instanceof Traversable)
			{
				throw new ValidationException($this->getCurrentPath() . ' must be an array');
			}

			$result = array();
			$index  = 0;

			foreach($data as $value)
			{
				array_push($this->pathStack, $index);

				$result[] = $this->recTraverse($value, $property->getPrototype(), $visitor, $type);

				array_pop($this->pathStack);

				$index++;
			}

			return $visitor->visitArray($result, $property, $this->getCurrentPath());
		}
		else if($property instanceof Property\BooleanType)
		{
			return $visitor->visitBoolean($data, $property, $this->getCurrentPath());
		}
		else if($property instanceof Property\ChoiceType)
		{
			$properties = $property->getProperties();
			$matches    = array();
			foreach($property as $index => $prop)
			{
				$value = $this->match($data, $prop);
				if($value > 0)
				{
					$matches[$index] = $value;
				}
			}

			if(empty($matches))
			{
				throw new ValidationException($this->getCurrentPath() . ' must be one of the following objects [' . implode(', ', array_keys($properties)) . ']');
			}

			arsort($matches);

			return $this->recTraverse($data, $properties[key($matches)], $visitor, $type);
		}
		else if($property instanceof Property\ComplexType)
		{
			if($type == self::TYPE_INCOMING)
			{
				if($data instanceof \stdClass)
				{
					$data = (array) $data;
				}
				else
				{
					throw new ValidationException($this->getCurrentPath() . ' must be an object');
				}
			}
			else
			{
				$data = $this->normalizeToArray($data);
				if(!is_array($data))
				{
					throw new ValidationException($this->getCurrentPath() . ' must be an object');
				}
			}

			$result = new \stdClass();

			foreach($property as $key => $prop)
			{
				array_push($this->pathStack, $key);

				if(isset($data[$key]))
				{
					$result->$key = $this->recTraverse($data[$key], $prop, $visitor, $type);
				}
				else if($prop->isRequired())
				{
					throw new ValidationException($this->getCurrentPath() . ' is required');
				}

				array_pop($this->pathStack);
			}

			if($type == self::TYPE_INCOMING)
			{
				// check whether there are fields which not exist in the schema
				foreach($data as $key => $value)
				{
					if(!$property->has($key))
					{
						throw new ValidationException($this->getCurrentPath() . ' property "' . $key . '" does not exist');
					}
				}
			}

			return $visitor->visitComplex($result, $property, $this->getCurrentPath());
		}
		else if($property instanceof Property\DateTimeType)
		{
			return $visitor->visitDateTime($data, $property, $this->getCurrentPath());
		}
		else if($property instanceof Property\DateType)
		{
			return $visitor->visitDate($data, $property, $this->getCurrentPath());
		}
		else if($property instanceof Property\DurationType)
		{
			return $visitor->visitDuration($data, $property, $this->getCurrentPath());
		}
		else if($property instanceof Property\FloatType)
		{
			return $visitor->visitFloat($data, $property, $this->getCurrentPath());
		}
		else if($property instanceof Property\IntegerType)
		{
			return $visitor->visitInteger($data, $property, $this->getCurrentPath());
		}
		else if($property instanceof Property\TimeType)
		{
			return $visitor->visitTime($data, $property, $this->getCurrentPath());
		}
		else if($property instanceof Property\StringType)
		{
			return $visitor->visitString($data, $property, $this->getCurrentPath());
		}
	}

	/**
	 * Returns an value indicating how much the given data structure matches 
	 * this type
	 *
	 * @param mixed $data
	 * @return integer
	 */
	protected function match($data, Property\ComplexType $property)
	{
		$data = $this->normalizeToArray($data);

		if(is_array($data))
		{
			$properties = $property->getProperties();
			$match      = 0;

			foreach($properties as $name => $property)
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

			return $match / count($properties);
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

	protected function getCurrentPath()
	{
		return '/' . implode('/', $this->pathStack);
	}
}
