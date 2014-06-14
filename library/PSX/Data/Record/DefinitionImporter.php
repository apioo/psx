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

namespace PSX\Data\Record;

use InvalidArgumentException;
use PSX\Data\Record;
use PSX\Data\Record\Definition\CollectionInterface;
use PSX\Data\Record\Definition\Property;
use PSX\Data\Record\DefinitionInterface;
use ReflectionClass;
use RuntimeException;

/**
 * Importer wich imports data into an record based on an definition
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DefinitionImporter implements ImporterInterface
{
	protected $collection;

	public function __construct(CollectionInterface $collection)
	{
		$this->collection = $collection;
	}

	public function import($name, $data)
	{
		$definition = $this->collection->get($name);

		if(empty($definition))
		{
			throw new InvalidArgumentException('Could not find record definition ' . $name);	
		}

		if(!is_array($data))
		{
			throw new InvalidArgumentException('Data must be an array');
		}

		return $this->getRecordByDefinition($definition, $data);
	}

	protected function getRecordByDefinition(DefinitionInterface $definition, array $data)
	{
		$properties = $definition->getProperties();
		$data       = array_intersect_key($data, $properties);
		$fields     = array();

		foreach($data as $key => $value)
		{
			$fields[$key] = $this->getValue($value, $properties[$key]);
		}

		// check whether we have fields which are required but not available or
		// set the default value
		foreach($properties as $key => $property)
		{
			if(!isset($fields[$key]))
			{
				if($property->isRequired())
				{
					$title = $property->getTitle() == null ? $property->getName() : $property->getTitle();

					throw new RuntimeException('Required field "' . $title . '" not set');
				}
				else
				{
					$fields[$key] = $property->getDefault();
				}
			}
		}

		return new Record($definition->getName(), $fields);
	}

	protected function getValue($value, Property $property)
	{
		switch($property->getType())
		{
			case 'integer':
				$value = (int) $value;
				break;

			case 'float':
				$value = (float) $value;
				break;

			case 'boolean':
				$value = $value === 'false' ? false : (bool) $value;
				break;

			case 'array':
				$value = (array) $value;
				break;

			case 'object':
				$reference = $property->getReference();
				if(!empty($reference) && !empty($value) && is_array($value))
				{
					$definition = $this->collection->get($reference);
					$value      = $this->getRecordByDefinition($definition, $value);

					return $value;
				}
				break;

			default:
			case 'string':
				$value = (string) $value;
				break;
		}

		// array
		if($property->getType() == 'array')
		{
			$childProperty = $property->getChild();
			if(!$childProperty instanceof Property)
			{
				$childProperty = new Property(null, 'string');
			}

			$result = array();
			foreach($value as $val)
			{
				$result[] = $this->getValue($val, $childProperty);
			}

			$value = $result;
		}

		// class
		$className = $property->getClass();

		if(!empty($className) && class_exists($className))
		{
			$class = new ReflectionClass($className);

			if($class->implementsInterface('PSX\Data\FactoryInterface'))
			{
				$record = $class->newInstance()->factory($value);

				if($record !== null)
				{
					$value = $this->import($record, $value);
				}
			}
			else if($class->implementsInterface('PSX\Data\BuilderInterface'))
			{
				$value = $class->newInstance()->build($value);
			}
			else
			{
				$value = $class->newInstance($value);
			}
		}

		return $value;
	}
}
