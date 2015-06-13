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
use PSX\Uri;

/**
 * ValidationVisitorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidationVisitorTest extends \PHPUnit_Framework_TestCase
{
	public function testVisitArray()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getArray('test')->setPrototype(Property::getString('foo'));

		$this->assertTrue($visitor->visitArray(array(), $property, ''));
		$this->assertTrue($visitor->visitArray(array('foo'), $property, ''));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testVisitArrayNoPrototype()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getArray('test');

		$this->assertTrue($visitor->visitArray(array(), $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitArrayMinLength()
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
	public function testVisitArrayMaxLength()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getArray('test')->setPrototype(Property::getString('foo'));
		$property->setMaxLength(1);

		$this->assertEquals(1, $property->getMaxLength());

		$visitor->visitArray(array('foo', 'bar'), $property, '');
	}

	public function testVisitBoolean()
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
	public function testVisitBooleanInvalidString()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getBoolean('test');

		$visitor->visitBoolean('foo', $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitBooleanInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getBoolean('test');

		$visitor->visitBoolean(4, $property, '');
	}

	public function testVisitBooleanNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getBoolean('test');

		$this->assertTrue($visitor->visitBoolean(null, $property, ''));
	}

	public function testVisitComplex()
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
	public function testVisitComplexNoProperties()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getComplex('test');

		$visitor->visitComplex(new \stdClass(), $property, '');
	}

	public function testVisitDateTime()
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
	public function testVisitDateTimeInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDateTime('test');

		$visitor->visitDateTime('foo', $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitDateTimeInvalidTimezone()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDateTime('test');

		$visitor->visitDateTime('2002-10-10T17:00:00+25:00', $property, '');
	}

	public function testVisitDateTimeNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDateTime('test');

		$this->assertTrue($visitor->visitDateTime(null, $property, ''));
	}

	public function testVisitDateTimeDateTime()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDateTime('test');

		$this->assertTrue($visitor->visitDateTime(new \DateTime(), $property, ''));
	}

	public function testVisitDate()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDate('test');

		$this->assertTrue($visitor->visitDate('2000-01-01', $property, ''));
		$this->assertTrue($visitor->visitDate('2000-01-01+13:00', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitDateInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDate('test');

		$visitor->visitDate('foo', $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitDateInvalidTimezone()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDate('test');

		$visitor->visitDate('2000-01-01+25:00', $property, '');
	}

	public function testVisitDateNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDate('test');

		$this->assertTrue($visitor->visitDate(null, $property, ''));
	}

	public function testVisitDateDateTime()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDate('test');

		$this->assertTrue($visitor->visitDate(new \DateTime(), $property, ''));
	}

	public function testVisitDuration()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDuration('test');

		$this->assertTrue($visitor->visitDuration('P1D', $property, ''));
		$this->assertTrue($visitor->visitDuration('P1DT12H', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitDurationInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDuration('test');

		$visitor->visitDuration('foo', $property, '');
	}

	public function testVisitDurationNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDuration('test');

		$this->assertTrue($visitor->visitDuration(null, $property, ''));
	}

	public function testVisitDurationDateTime()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getDuration('test');

		$this->assertTrue($visitor->visitDuration(new \DateInterval('P1Y'), $property, ''));
	}

	public function testVisitFloat()
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
	public function testVisitFloatInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getFloat('test');

		$this->assertTrue($visitor->visitFloat('foo', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitFloatInvalidType()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getFloat('test');

		$this->assertTrue($visitor->visitFloat(new \stdClass(), $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitFloatMin()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getFloat('test')->setMin(2.4);

		$this->assertTrue($visitor->visitFloat(2.4, $property, ''));

		$visitor->visitFloat(2.3, $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitFloatMax()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getFloat('test')->setMax(2.4);

		$this->assertTrue($visitor->visitFloat(2.4, $property, ''));

		$visitor->visitFloat(2.5, $property, '');
	}

	public function testVisitFloatNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getFloat('test');

		$this->assertTrue($visitor->visitFloat(null, $property, ''));
	}

	public function testVisitInteger()
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
	public function testVisitIntegerInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test');

		$this->assertTrue($visitor->visitInteger('foo', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitIntegerInvalidFormatFraction()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test');

		$this->assertTrue($visitor->visitInteger('1.2', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitIntegerInvalidFormatType()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test');

		$this->assertTrue($visitor->visitInteger(1.2, $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitIntegerInvalidType()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test');

		$this->assertTrue($visitor->visitInteger(new \stdClass(), $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitIntegerMin()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test')->setMin(2);

		$this->assertTrue($visitor->visitInteger(2, $property, ''));

		$visitor->visitInteger(1, $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitIntegerMax()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test')->setMax(2);

		$this->assertTrue($visitor->visitInteger(2, $property, ''));

		$visitor->visitInteger(3, $property, '');
	}

	public function testVisitIntegerNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getInteger('test');

		$this->assertTrue($visitor->visitInteger(null, $property, ''));
	}

	public function testVisitString()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getString('test');

		$this->assertTrue($visitor->visitString('foo', $property, ''));
		// __toString object validates to true
		$this->assertTrue($visitor->visitString(new Uri('foo:bar'), $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitStringInvalidType()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getString('test');

		$visitor->visitString(array(), $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitIntegerMinLength()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getString('test')->setMinLength(2);

		$this->assertTrue($visitor->visitString('fo', $property, ''));
		$this->assertTrue($visitor->visitString('foo', $property, ''));

		$visitor->visitString('f', $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitIntegerMaxLength()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getString('test')->setMaxLength(2);

		$this->assertTrue($visitor->visitString('fo', $property, ''));
		$this->assertTrue($visitor->visitString('f', $property, ''));

		$visitor->visitString('foo', $property, '');
	}

	public function testVisitStringNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getString('test');

		$this->assertTrue($visitor->visitString(null, $property, ''));
	}

	public function testVisitTime()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getTime('test');

		$this->assertTrue($visitor->visitTime('13:37:00', $property, ''));
		$this->assertTrue($visitor->visitTime('13:37:00+13:00', $property, ''));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitTimeInvalidFormat()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getTime('test');

		$visitor->visitTime('foo', $property, '');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testVisitTimeInvalidTimezone()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getTime('test');

		$visitor->visitTime('13:37:00+25:00', $property, '');
	}

	public function testVisitTimeNull()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getTime('test');

		$this->assertTrue($visitor->visitTime(null, $property, ''));
	}

	public function testVisitTimeDateTime()
	{
		$visitor  = new ValidationVisitor();
		$property = Property::getTime('test');

		$this->assertTrue($visitor->visitTime(new \DateTime(), $property, ''));
	}
}
