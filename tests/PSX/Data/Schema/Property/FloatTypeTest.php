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

use PSX\Data\Schema\Property;

/**
 * FloatTypeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FloatTypeTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$property = Property::getFloat('test');

		$this->assertTrue($property->validate(1));
		$this->assertTrue($property->validate(1.2));
		$this->assertTrue($property->validate(-1.2));
		$this->assertTrue($property->validate('1'));
		$this->assertTrue($property->validate('1.2'));
		$this->assertTrue($property->validate('-1.2'));
		$this->assertTrue($property->validate('1.2E4'));
		$this->assertTrue($property->validate('1.2e4'));
		$this->assertTrue($property->validate('1.2e+4'));
		$this->assertTrue($property->validate('1.2e-4'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidFormat()
	{
		$property = Property::getFloat('test');

		$this->assertTrue($property->validate('foo'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidType()
	{
		$property = Property::getFloat('test');

		$this->assertTrue($property->validate(new \stdClass()));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateMin()
	{
		$property = Property::getFloat('test')->setMin(2.4);

		$this->assertTrue($property->validate(2.4));

		$property->validate(2.3);
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateMax()
	{
		$property = Property::getFloat('test')->setMax(2.4);

		$this->assertTrue($property->validate(2.4));

		$property->validate(2.5);
	}

	public function testValidateNull()
	{
		$property = Property::getFloat('test');

		$this->assertTrue($property->validate(null));
	}

	public function testAssimilate()
	{
		$property = Property::getFloat('test');

		$this->assertInternalType('float', $property->assimilate('1'));
		$this->assertEquals(1, $property->assimilate(1));
		$this->assertEquals(1.2, $property->assimilate(1.2));
		$this->assertEquals(-1.2, $property->assimilate(-1.2));
		$this->assertEquals(1, $property->assimilate('1'));
		$this->assertEquals(1.2, $property->assimilate('1.2'));
		$this->assertEquals(-1.2, $property->assimilate('-1.2'));
		$this->assertEquals(12000.0, $property->assimilate('1.2E4'));
		$this->assertEquals(12000.0, $property->assimilate('1.2e4'));
		$this->assertEquals(12000.0, $property->assimilate('1.2e+4'));
		$this->assertEquals(0.00012, $property->assimilate('1.2e-4'));
	}

	public function testAssimilateInvalidFormat()
	{
		$property = Property::getFloat('test');

		$this->assertEquals(0, $property->assimilate('foo'));
	}

	public function testGetId()
	{
		$property = Property::getFloat('test');

		$this->assertEquals('d910a17e4a7a672df119e67b08b0f3cf', $property->getId());
	}

	public function testGetTypeName()
	{
		$this->assertEquals('float', Property::getFloat('test')->getTypeName());
	}
}
