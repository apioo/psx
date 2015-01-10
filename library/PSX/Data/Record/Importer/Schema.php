<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Record\Importer;

use InvalidArgumentException;
use PSX\Data\Record as DataRecord;
use PSX\Data\Record\FactoryFactory;
use PSX\Data\Record\ImporterInterface;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\ValidatorInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Imports data based on an given schema. Validates also the data if an 
 * validator was set
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Schema implements ImporterInterface
{
	protected $validator;
	protected $factory;

	public function __construct(ValidatorInterface $validator, FactoryFactory $factory)
	{
		$this->validator = $validator;
		$this->factory   = $factory;
	}

	public function accept($schema)
	{
		return $schema instanceof SchemaInterface;
	}

	public function import($schema, $data)
	{
		if(!$schema instanceof SchemaInterface)
		{
			throw new InvalidArgumentException('Schema must be an instanceof PSX\Data\SchemaInterface');
		}

		if(!is_array($data))
		{
			throw new InvalidArgumentException('Data must be an array');
		}

		$this->validator->validate($schema, $data);

		return $this->recImport($schema->getDefinition(), $data);
	}

	protected function recImport(PropertyInterface $type, $data)
	{
		$reference = $type->getReference();

		if($type instanceof Property\ComplexType)
		{
			$children = $type->getChildren();
			$fields   = array();

			foreach($children as $child)
			{
				if(isset($data[$child->getName()]))
				{
					$fields[$child->getName()] = $this->recImport($child, $data[$child->getName()]);
				}
			}

			if(empty($reference))
			{
				return new DataRecord($type->getName(), $fields);
			}
			else
			{
				return $this->getComplexReference($reference, $fields);
			}
		}
		else if($type instanceof Property\ArrayType)
		{
			$prototype = $type->getPrototype();
			$values    = array();

			foreach($data as $value)
			{
				$values[] = $this->recImport($prototype, $value);
			}

			return $values;
		}
		else if($type instanceof Property\Boolean)
		{
			$data = $data === 'false' ? false : (bool) $data;

			return empty($reference) ? $data : $this->getSimpleReference($reference, $data);
		}
		else if($type instanceof Property\Date || $type instanceof Property\DateTime)
		{
			return empty($reference) ? new \DateTime($data) : $this->getSimpleReference($reference, $data);
		}
		else if($type instanceof Property\Duration)
		{
			return empty($reference) ? new \DateInterval($data) : $this->getSimpleReference($reference, $data);
		}
		else if($type instanceof Property\Float)
		{
			$data = (float) $data;

			return empty($reference) ? $data : $this->getSimpleReference($reference, $data);
		}
		else if($type instanceof Property\Integer)
		{
			$data = (int) $data;

			return empty($reference) ? $data : $this->getSimpleReference($reference, $data);
		}
		else
		{
			$data = (string) $data;

			return empty($reference) ? $data : $this->getSimpleReference($reference, $data);
		}
	}

	protected function getComplexReference($reference, array $fields)
	{
		$class = new ReflectionClass($reference);

		if($class->implementsInterface('PSX\Data\RecordInterface'))
		{
			$record = $class->newInstance();

			foreach($fields as $key => $value)
			{
				try
				{
					$methodName = 'set' . ucfirst($key);
					$method     = $class->getMethod($methodName);

					if($method instanceof ReflectionMethod)
					{
						$method->invokeArgs($record, array($value));
					}
				}
				catch(ReflectionException $e)
				{
					// method does not exist
				}
			}

			return $record;
		}
		else if($class->implementsInterface('PSX\Data\Record\FactoryInterface'))
		{
			return $this->factory->getFactory($reference)->factory($fields, $this);
		}
		else
		{
			return $class->newInstance($fields);
		}
	}

	protected function getSimpleReference($reference, $value)
	{
		try
		{
			$class       = new ReflectionClass($reference);
			$constructor = $class->getConstructor();

			if($constructor instanceof ReflectionMethod && $constructor->getNumberOfRequiredParameters() == 1)
			{
				return $class->newInstance($value);
			}
		}
		catch(ReflectionException $e)
		{
			// class does not exist
		}

		return $value;
	}
}
