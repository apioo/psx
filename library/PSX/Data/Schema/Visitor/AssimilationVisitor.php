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

namespace PSX\Data\Schema\Visitor;

use PSX\Data\Record;
use PSX\Data\Record\FactoryFactory;
use PSX\Data\Record\ImporterInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\ValidationException;
use PSX\Data\SchemaInterface;
use PSX\DateTime;
use PSX\DateTime\Date;
use PSX\DateTime\Duration;
use PSX\DateTime\Time;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

/**
 * AssimilationVisitor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AssimilationVisitor extends ValidationVisitor
{
	protected $validate;
	protected $factory;

	/**
	 * Transforms data according to the schema. If $validation is true each 
	 * value is also validated and a proper exception is thrown if its not 
	 * valid. In most cases we validate only incoming data
	 *
	 * @param boolean $validate
	 * @param PSX\Data\Record\FactoryFactory $factory
	 */
	public function __construct($validate = false, FactoryFactory $factory = null)
	{
		$this->validate = $validate;
		$this->factory  = $factory;
	}

	public function visitArray(array $data, Property\ArrayType $property, $path)
	{
		if($this->validate)
		{
			parent::visitArray($data, $property, $path);
		}

		return $this->createSimpleProperty($data, $property);
	}

	public function visitBoolean($data, Property\BooleanType $property, $path)
	{
		if($this->validate)
		{
			parent::visitBoolean($data, $property, $path);
		}
		else
		{
			$this->assertRequired($data, $property, $path);
		}

		$data = $data === 'false' ? false : (bool) $data;

		return $this->createSimpleProperty($data, $property);
	}

	public function visitComplex(\stdClass $data, Property\ComplexType $property, $path)
	{
		if($this->validate)
		{
			parent::visitComplex($data, $property, $path);
		}

		$reference = $property->getReference();
		if(!empty($reference))
		{
			$class = new ReflectionClass($reference);

			if($class->implementsInterface('PSX\Data\RecordInterface'))
			{
				$record = $class->newInstance();

				foreach($data as $key => $val)
				{
					try
					{
						$methodName = 'set' . ucfirst($key);
						$method     = $class->getMethod($methodName);

						if($method instanceof ReflectionMethod)
						{
							$method->invokeArgs($record, array($val));
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
				if($this->factory !== null)
				{
					return $this->factory->getFactory($reference)->factory($data);
				}
				else
				{
					throw new RuntimeException('No factory provided to resolve ' . $path);
				}
			}
			else
			{
				try
				{
					$class       = new ReflectionClass($reference);
					$constructor = $class->getConstructor();

					if($constructor instanceof ReflectionMethod && $constructor->getNumberOfRequiredParameters() == 1)
					{
						return $class->newInstance($data);
					}
				}
				catch(ReflectionException $e)
				{
					// class does not exist
				}
			}
		}

		return new Record($property->getName(), (array) $data);
	}

	public function visitDateTime($data, Property\DateTimeType $property, $path)
	{
		if($this->validate)
		{
			parent::visitDateTime($data, $property, $path);
		}
		else
		{
			$this->assertRequired($data, $property, $path);
		}

		if($data instanceof \DateTime)
		{
			return $this->createSimpleProperty($data, $property);
		}

		try
		{
			return $this->createSimpleProperty(new DateTime($data), $property);
		}
		catch(\Exception $e)
		{
			throw new ValidationException($path . ' must be an valid date-time format (full-date "T" full-time) [RFC3339]');
		}
	}

	public function visitDate($data, Property\DateType $property, $path)
	{
		if($this->validate)
		{
			parent::visitDate($data, $property, $path);
		}
		else
		{
			$this->assertRequired($data, $property, $path);
		}

		if($data instanceof \DateTime)
		{
			return $this->createSimpleProperty(Date::fromDateTime($data), $property);
		}

		try
		{
			return $this->createSimpleProperty(new Date($data), $property);
		}
		catch(\Exception $e)
		{
			throw new ValidationException($path . ' must be an valid full-date format (date-fullyear "-" date-month "-" date-mday) [RFC3339]');
		}
	}

	public function visitDuration($data, Property\DurationType $property, $path)
	{
		if($this->validate)
		{
			parent::visitDuration($data, $property, $path);
		}
		else
		{
			$this->assertRequired($data, $property, $path);
		}

		if($data instanceof \DateInterval)
		{
			return $this->createSimpleProperty(Duration::fromDateInterval($data), $property);
		}

		try
		{
			return $this->createSimpleProperty(new Duration($data), $property);
		}
		catch(\Exception $e)
		{
			throw new ValidationException($path . ' must be an valid duration format [ISO8601]');
		}
	}

	public function visitFloat($data, Property\FloatType $property, $path)
	{
		if($this->validate)
		{
			parent::visitFloat($data, $property, $path);
		}
		else
		{
			$this->assertRequired($data, $property, $path);
		}

		$data = (float) $data;

		return $this->createSimpleProperty($data, $property);
	}

	public function visitInteger($data, Property\IntegerType $property, $path)
	{
		if($this->validate)
		{
			parent::visitInteger($data, $property, $path);
		}
		else
		{
			$this->assertRequired($data, $property, $path);
		}

		$data = (int) $data;

		return $this->createSimpleProperty($data, $property);
	}

	public function visitString($data, Property\StringType $property, $path)
	{
		if($this->validate)
		{
			parent::visitString($data, $property, $path);
		}
		else
		{
			$this->assertRequired($data, $property, $path);
		}

		// data from an blob column gets returned as resource
		if(is_resource($data))
		{
			$data = base64_encode(stream_get_contents($data, -1, 0));
		}
		else
		{
			$data = (string) $data;
		}

		return $this->createSimpleProperty($data, $property);
	}

	public function visitTime($data, Property\TimeType $property, $path)
	{
		if($this->validate)
		{
			parent::visitTime($data, $property, $path);
		}
		else
		{
			$this->assertRequired($data, $property, $path);
		}

		if($data instanceof \DateTime)
		{
			return $this->createSimpleProperty(Time::fromDateTime($data), $property);
		}

		try
		{
			return $this->createSimpleProperty(new Time($data), $property);
		}
		catch(\Exception $e)
		{
			throw new ValidationException($path . ' must be an valid full-time format (partial-time time-offset) [RFC3339]');
		}
	}

	protected function createSimpleProperty($data, PropertyInterface $property)
	{
		$reference = $property->getReference();
		if(!empty($reference))
		{
			try
			{
				$class       = new ReflectionClass($reference);
				$constructor = $class->getConstructor();

				if($constructor instanceof ReflectionMethod && $constructor->getNumberOfRequiredParameters() == 1)
				{
					return $class->newInstance($data);
				}
			}
			catch(ReflectionException $e)
			{
				// class does not exist
			}
		}

		return $data;
	}
}
