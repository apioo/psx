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

use PSX\Data\SchemaAbstract;
use PSX\Data\Schema\Property;
use PSX\Test\Environment;

/**
 * ValidationVisitorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidationVisitorTest extends \PHPUnit_Framework_TestCase
{
	public function testArrayValidate()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getArray('test')->setPrototype(Property::getString('foo'));

		$this->assertTrue($visitor->visitArray(array(), $property, ''));
		$this->assertTrue($visitor->visitArray(array('foo'), $property, ''));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testArrayValidateNoPrototype()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getArray('test');

		$this->assertTrue($visitor->visitArray(array(), $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testArrayValidateMinLength()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getArray('test')->setPrototype(Property::getString('foo'));
		$property->setMinLength(1);

		$this->assertEquals(1, $property->getMinLength());

		$visitor->visitArray(array(), $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testArrayValidateMaxLength()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getArray('test')->setPrototype(Property::getString('foo'));
		$property->setMaxLength(1);

		$this->assertEquals(1, $property->getMaxLength());

		$visitor->visitArray(array('foo', 'bar'), $property, '');
	}

	public function testBooleanValidate()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getBoolean('test');

		$this->assertTrue($visitor->visitBoolean(true, $property, ''));
		$this->assertTrue($visitor->visitBoolean(false, $property, ''));
		$this->assertTrue($visitor->visitBoolean(1, $property, ''));
		$this->assertTrue($visitor->visitBoolean(0, $property, ''));
		$this->assertTrue($visitor->visitBoolean('1', $property, ''));
		$this->assertTrue($visitor->visitBoolean('0', $property, ''));
		$this->assertTrue($visitor->visitBoolean('true', $property, ''));
		$this->assertTrue($visitor->visitBoolean('false', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testBooleanValidateInvalidString()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getBoolean('test');

		$visitor->visitBoolean('foo', $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testBooleanValidateInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getBoolean('test');

		$visitor->visitBoolean(4, $property, '');
	}

	public function testBooleanValidateNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getBoolean('test');

		$this->assertTrue($visitor->visitBoolean(null, $property, ''));
	}

	public function testComplexValidate()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getComplex('test')
			->add(Property::getString('foo'))
			->add(Property::getString('bar'));

		$this->assertTrue($visitor->visitComplex(new \stdClass(), $property, ''));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testComplexValidateNoProperties()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getComplex('test');

		$visitor->visitComplex(new \stdClass(), $property, '');
	}

	public function testDateTimeValidate()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDateTime('test');

		$this->assertTrue($visitor->visitDateTime('2002-10-10T17:00:00', $property, ''));
		$this->assertTrue($visitor->visitDateTime('2002-10-10T17:00:00Z', $property, ''));
		$this->assertTrue($visitor->visitDateTime('2002-10-10T17:00:00+13:00', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testDateTimeValidateInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDateTime('test');

		$visitor->visitDateTime('foo', $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testDateTimeValidateInvalidTimezone()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDateTime('test');

		$visitor->visitDateTime('2002-10-10T17:00:00+25:00', $property, '');
	}

	public function testDateTimeValidateNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDateTime('test');

		$this->assertTrue($visitor->visitDateTime(null, $property, ''));
	}

	public function testDateTimeValidateDateTime()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDateTime('test');

		$this->assertTrue($visitor->visitDateTime(new \DateTime(), $property, ''));
	}

	public function testDateValidate()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDate('test');

		$this->assertTrue($visitor->visitDate('2000-01-01', $property, ''));
		$this->assertTrue($visitor->visitDate('2000-01-01+13:00', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testDateValidateInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDate('test');

		$visitor->visitDate('foo', $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testDateValidateInvalidTimezone()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDate('test');

		$visitor->visitDate('2000-01-01+25:00', $property, '');
	}

	public function testDateValidateNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDate('test');

		$this->assertTrue($visitor->visitDate(null, $property, ''));
	}

	public function testDateValidateDateTime()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDate('test');

		$this->assertTrue($visitor->visitDate(new \DateTime(), $property, ''));
	}

	public function testDurationValidate()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDuration('test');

		$this->assertTrue($visitor->visitDuration('P1D', $property, ''));
		$this->assertTrue($visitor->visitDuration('P1DT12H', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testDurationValidateInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDuration('test');

		$visitor->visitDuration('foo', $property, '');
	}

	public function testDurationValidateNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDuration('test');

		$this->assertTrue($visitor->visitDuration(null, $property, ''));
	}

	public function testDurationValidateDateTime()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDuration('test');

		$this->assertTrue($visitor->visitDuration(new \DateInterval('P1Y'), $property, ''));
	}

	public function testFloatValidate()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getFloat('test');

		$this->assertTrue($visitor->visitFloat(1, $property, ''));
		$this->assertTrue($visitor->visitFloat(1.2, $property, ''));
		$this->assertTrue($visitor->visitFloat(-1.2, $property, ''));
		$this->assertTrue($visitor->visitFloat('1', $property, ''));
		$this->assertTrue($visitor->visitFloat('1.2', $property, ''));
		$this->assertTrue($visitor->visitFloat('-1.2', $property, ''));
		$this->assertTrue($visitor->visitFloat('1.2E4', $property, ''));
		$this->assertTrue($visitor->visitFloat('1.2e4', $property, ''));
		$this->assertTrue($visitor->visitFloat('1.2e+4', $property, ''));
		$this->assertTrue($visitor->visitFloat('1.2e-4', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testFloatValidateInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getFloat('test');

		$this->assertTrue($visitor->visitFloat('foo', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testFloatValidateInvalidType()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getFloat('test');

		$this->assertTrue($visitor->visitFloat(new \stdClass(), $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testFloatValidateMin()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getFloat('test')->setMin(2.4);

		$this->assertTrue($visitor->visitFloat(2.4, $property, ''));

		$visitor->visitFloat(2.3, $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testFloatValidateMax()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getFloat('test')->setMax(2.4);

		$this->assertTrue($visitor->visitFloat(2.4, $property, ''));

		$visitor->visitFloat(2.5, $property, '');
	}

	public function testFloatValidateNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getFloat('test');

		$this->assertTrue($visitor->visitFloat(null, $property, ''));
	}

	public function testIntegerValidate()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test');

		$this->assertTrue($visitor->visitInteger(4, $property, ''));
		$this->assertTrue($visitor->visitInteger('4', $property, ''));
		$this->assertTrue($visitor->visitInteger('+4', $property, ''));
		$this->assertTrue($visitor->visitInteger('-4', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testIntegerValidateInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test');

		$this->assertTrue($visitor->visitInteger('foo', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testIntegerValidateInvalidFormatFraction()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test');

		$this->assertTrue($visitor->visitInteger('1.2', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testIntegerValidateInvalidFormatType()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test');

		$this->assertTrue($visitor->visitInteger(1.2, $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testIntegerValidateInvalidType()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test');

		$this->assertTrue($visitor->visitInteger(new \stdClass(), $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testIntegerValidateMin()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test')->setMin(2);

		$this->assertTrue($visitor->visitInteger(2, $property, ''));

		$visitor->visitInteger(1, $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testIntegerValidateMax()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test')->setMax(2);

		$this->assertTrue($visitor->visitInteger(2, $property, ''));

		$visitor->visitInteger(3, $property, '');
	}

	public function testIntegerValidateNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test');

		$this->assertTrue($visitor->visitInteger(null, $property, ''));
	}
}
