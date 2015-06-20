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

use InvalidArgumentException;
use PSX\Data\Record;
use PSX\Data\Record\FactoryInterface;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\ValidationException;
use PSX\Data\Schema\VisitorInterface;
use PSX\DateTime;
use PSX\DateTime\Date;
use PSX\DateTime\Time;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

/**
 * ValidationVisitor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidationVisitor implements VisitorInterface
{
	public function visitArray(array $data, Property\ArrayType $property, $path)
	{
		$this->assertCompositeProperties($property, $path);

		if($property->getMinLength() !== null)
		{
			if(count($data) < $property->getMinLength())
			{
				throw new ValidationException($path . ' must contain more then ' . $property->getMinLength() . ' elements');
			}
		}

		if($property->getMaxLength() !== null)
		{
			if(count($data) > $property->getMaxLength())
			{
				throw new ValidationException($path . ' must contain less then ' . $property->getMaxLength() . ' elements');
			}
		}

		return true;
	}

	public function visitBoolean($data, Property\BooleanType $property, $path)
	{
		$this->assertRequired($data, $property, $path);

		if($data === null)
		{
			return true;
		}

		if(is_bool($data))
		{
			return true;
		}
		else if(is_scalar($data))
		{
			$result = preg_match('/^true|false|1|0$/', $data);

			if($result)
			{
				return true;
			}
		}

		throw new ValidationException($path . ' must be boolean');
	}

	public function visitComplex(\stdClass $data, Property\ComplexType $property, $path)
	{
		$this->assertCompositeProperties($property, $path);

		return true;
	}

	public function visitDateTime($data, Property\DateTimeType $property, $path)
	{
		$this->assertRequired($data, $property, $path);

		if($data === null)
		{
			return true;
		}
		else if($data instanceof \DateTime)
		{
			return true;
		}
		else if(is_string($data))
		{
			$result = preg_match('/^' . \PSX\DateTime::getPattern() . '$/', $data);

			if($result)
			{
				return true;
			}
		}

		throw new ValidationException($path . ' must be an valid date-time format (full-date "T" full-time) [RFC3339]');
	}

	public function visitDate($data, Property\DateType $property, $path)
	{
		$this->assertRequired($data, $property, $path);

		if($data === null)
		{
			return true;
		}
		else if($data instanceof \DateTime)
		{
			return true;
		}
		else if(is_string($data))
		{
			$result = preg_match('/^' . \PSX\DateTime\Date::getPattern() . '$/', $data);

			if($result)
			{
				return true;
			}
		}

		throw new ValidationException($path . ' must be an valid full-date format (date-fullyear "-" date-month "-" date-mday) [RFC3339]');
	}

	public function visitDuration($data, Property\DurationType $property, $path)
	{
		$this->assertRequired($data, $property, $path);

		if($data === null)
		{
			return true;
		}
		else if($data instanceof \DateInterval)
		{
			return true;
		}
		else if(is_string($data))
		{
			$result = preg_match('/^' . \PSX\DateTime\Duration::getPattern() . '$/', $data);

			if($result)
			{
				return true;
			}
		}

		throw new ValidationException($path . ' must be an valid duration format [ISO8601]');
	}

	public function visitFloat($data, Property\FloatType $property, $path)
	{
		$this->assertRequired($data, $property, $path);

		if($data === null)
		{
			return true;
		}
		else if(is_float($data))
		{
		}
		else if(is_int($data))
		{
			$data = (float) $data;
		}
		else if(is_string($data))
		{
			$result = preg_match('/^(\+|-)?([0-9]+(\.[0-9]*)?|\.[0-9]+)([Ee](\+|-)?[0-9]+)?$/', $data);

			if($result)
			{
				$data = (float) $data;
			}
			else
			{
				throw new ValidationException($path . ' must be an float');
			}
		}
		else
		{
			throw new ValidationException($path . ' must be an float');
		}

		$this->assertDecimalConstraints($data, $property, $path);

		return true;
	}

	public function visitInteger($data, Property\IntegerType $property, $path)
	{
		$this->assertRequired($data, $property, $path);

		if($data === null)
		{
			return true;
		}
		else if(is_int($data))
		{
		}
		else if(is_string($data))
		{
			$result = preg_match('/^[\-+]?[0-9]+$/', $data);

			if($result)
			{
				$data = (int) $data;
			}
			else
			{
				throw new ValidationException($path . ' must be an integer');
			}
		}
		else
		{
			throw new ValidationException($path . ' must be an integer');
		}

		$this->assertDecimalConstraints($data, $property, $path);

		return true;
	}

	public function visitString($data, Property\StringType $property, $path)
	{
		$this->assertRequired($data, $property, $path);

		// must be an string or an object which can be casted to an string
		if($data === null)
		{
			return true;
		}
		else if(is_string($data))
		{
		}
		else if(is_object($data) && method_exists($data, '__toString'))
		{
			$data = (string) $data;
		}
		else
		{
			throw new ValidationException($path . ' must be a string');
		}

		$this->assertPropertySimpleConstraints($data, $property, $path);

		if($property->getMinLength() !== null)
		{
			if(strlen($data) < $property->getMinLength())
			{
				throw new ValidationException($path . ' must contain more then ' . $property->getMinLength() . ' characters');
			}
		}

		if($property->getMaxLength() !== null)
		{
			if(strlen($data) > $property->getMaxLength())
			{
				throw new ValidationException($path . ' must contain less then ' . $property->getMaxLength() . ' characters');
			}
		}

		return true;
	}

	public function visitTime($data, Property\TimeType $property, $path)
	{
		$this->assertRequired($data, $property, $path);

		if($data === null)
		{
			return true;
		}
		else if($data instanceof \DateTime)
		{
			return true;
		}
		else if(is_string($data))
		{
			$result = preg_match('/^' . \PSX\DateTime\Time::getPattern() . '$/', $data);

			if($result)
			{
				return true;
			}
		}

		throw new ValidationException($path . ' must be an valid full-time format (partial-time time-offset) [RFC3339]');
	}

	protected function assertCompositeProperties(PropertyInterface $property, $path)
	{
		if(count($property) == 0)
		{
			throw new RuntimeException($path . ' has no properties');
		}
	}

	protected function assertRequired($data, PropertyInterface $property, $path)
	{
		if($property->isRequired() && $data === null)
		{
			throw new ValidationException('Property ' . $path . ' is required');
		}
	}

	protected function assertPropertySimpleConstraints($data, PropertyInterface $property, $path)
	{
		if($property->getPattern() !== null)
		{
			$result = preg_match('/^(' . $property->getPattern() . '){1}$/', $data);

			if(!$result)
			{
				throw new ValidationException($path . ' does not match pattern [' . $property->getPattern() . ']');
			}
		}

		if($property->getEnumeration() !== null)
		{
			if(!in_array($data, $property->getEnumeration()))
			{
				throw new ValidationException($path . ' is not in enumeration [' . implode(', ', $property->getEnumeration()) . ']');
			}
		}
	}

	protected function assertDecimalConstraints($data, PropertyInterface $property, $path)
	{
		if($property->getMax() !== null)
		{
			if($data > $property->getMax())
			{
				throw new ValidationException($path . ' must be lower or equal then ' . $property->getMax());
			}
		}

		if($property->getMin() !== null)
		{
			if($data < $property->getMin())
			{
				throw new ValidationException($path . ' must be greater or equal then ' . $property->getMin());
			}
		}
	}
}
