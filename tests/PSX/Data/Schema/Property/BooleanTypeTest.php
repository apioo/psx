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
 * BooleanTypeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BooleanTypeTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$property = Property::getBoolean('test');

		$this->assertTrue($property->validate(true));
		$this->assertTrue($property->validate(false));
		$this->assertTrue($property->validate(1));
		$this->assertTrue($property->validate(0));
		$this->assertTrue($property->validate('1'));
		$this->assertTrue($property->validate('0'));
		$this->assertTrue($property->validate('true'));
		$this->assertTrue($property->validate('false'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidString()
	{
		$property = Property::getBoolean('test');

		$property->validate('foo');
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidFormat()
	{
		$property = Property::getBoolean('test');

		$property->validate(4);
	}

	public function testValidateNull()
	{
		$property = Property::getBoolean('test');

		$this->assertTrue($property->validate(null));
	}

	public function testAssimilate()
	{
		$property = Property::getBoolean('test');

		$this->assertInternalType('boolean', $property->assimilate(1));
		$this->assertEquals(true, $property->assimilate(true));
		$this->assertEquals(false, $property->assimilate(false));
		$this->assertEquals(true, $property->assimilate(1));
		$this->assertEquals(false, $property->assimilate(0));
		$this->assertEquals(true, $property->assimilate('1'));
		$this->assertEquals(false, $property->assimilate('0'));
		$this->assertEquals(true, $property->assimilate('true'));
		$this->assertEquals(false, $property->assimilate('false'));
	}

	public function testAssimilateInvalidFormat()
	{
		$property = Property::getBoolean('test');

		$this->assertEquals(true, $property->assimilate(4));
	}

	public function testGetId()
	{
		$property = Property::getBoolean('test');

		$this->assertEquals('23ea6bea2cec4913f94999a6221dfe87', $property->getId());
	}

	public function testGetTypeName()
	{
		$this->assertEquals('boolean', Property::getBoolean('test')->getTypeName());
	}
}
