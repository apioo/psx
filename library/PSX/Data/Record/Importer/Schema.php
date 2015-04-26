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

namespace PSX\Data\Record\Importer;

use InvalidArgumentException;
use PSX\Data\Record as DataRecord;
use PSX\Data\Record\FactoryFactory;
use PSX\Data\Record\ImporterInterface;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\ValidatorInterface;
use PSX\DateTime;
use PSX\DateTime\Date;
use PSX\DateTime\Duration;
use PSX\DateTime\Time;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Imports data based on an given schema. Validates also the data if an 
 * validator was set
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

		if(!$data instanceof \stdClass)
		{
			throw new InvalidArgumentException('Data must be an stdClass');
		}

		$this->validator->validate($schema, $data);

		return $this->recImport($schema->getDefinition(), $data);
	}

	protected function recImport(PropertyInterface $type, $data)
	{
		$reference = $type->getReference();

		if($type instanceof Property\ComplexType)
		{
			$properties = $type->getProperties();
			$fields     = array();

			foreach($properties as $name => $property)
			{
				if(isset($data->$name))
				{
					$fields[$name] = $this->recImport($property, $data->$name);
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
		else if($type instanceof Property\DateTime)
		{
			return empty($reference) ? new DateTime($data) : $this->getSimpleReference($reference, $data);
		}
		else if($type instanceof Property\Date)
		{
			return empty($reference) ? new Date($data) : $this->getSimpleReference($reference, $data);
		}
		else if($type instanceof Property\Time)
		{
			return empty($reference) ? new Time($data) : $this->getSimpleReference($reference, $data);
		}
		else if($type instanceof Property\Duration)
		{
			return empty($reference) ? new Duration($data) : $this->getSimpleReference($reference, $data);
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
			return $this->factory->getFactory($reference)->factory((object) $fields, $this);
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
