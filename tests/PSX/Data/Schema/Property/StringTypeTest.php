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
use PSX\Url;

/**
 * StringTypeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StringTypeTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$property = Property::getString('test');

		$this->assertTrue($property->validate('foo'));
	}

	public function testValidateObjectToString()
	{
		$property = Property::getString('test');
		$property->setMinLength(3);

		$property->validate(new Url('http://google.com'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidFormat()
	{
		$property = Property::getString('test');
		$property->validate(new \stdClass());
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidFormatArray()
	{
		$property = Property::getString('test');
		$property->validate(array());
	}

	public function testMinLength()
	{
		$property = Property::getString('test');
		$property->setMinLength(3);

		$this->assertTrue($property->validate('foo'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testMinLengthInvalid()
	{
		$property = Property::getString('test');
		$property->setMinLength(3);

		$property->validate('fo');
	}

	public function testMaxLength()
	{
		$property = Property::getString('test');
		$property->setMaxLength(3);

		$this->assertTrue($property->validate('foo'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testMaxLengthInvalid()
	{
		$property = Property::getString('test');
		$property->setMaxLength(3);

		$property->validate('fooo');
	}

	public function testPattern()
	{
		$property = Property::getString('test');
		$property->setPattern('[A-z]+');

		$this->assertTrue($property->validate('foo'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testPatternInvalid()
	{
		$property = Property::getString('test');
		$property->setPattern('[A-z]');

		$property->validate('123');
	}

	public function testEnumeration()
	{
		$property = Property::getString('test');
		$property->setEnumeration(array('foo', 'bar'));

		$this->assertTrue($property->validate('foo'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testEnumerationInvalid()
	{
		$property = Property::getString('test');
		$property->setEnumeration(array('foo', 'bar'));

		$property->validate('test');
	}

	public function testValidateNull()
	{
		$property = Property::getString('test');

		$this->assertTrue($property->validate(null));
	}
	
	public function testGetId()
	{
		$property = Property::getString('test');

		$this->assertEquals('39af8630a1fa0361bbad04cd0ec0e9d8', $property->getId());
	}

	public function testGetTypeName()
	{
		$this->assertEquals('string', Property::getString('test')->getTypeName());
	}
}
