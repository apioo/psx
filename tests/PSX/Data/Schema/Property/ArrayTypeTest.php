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

/**
 * ArrayTypeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ArrayTypeTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$property = new ArrayType('test');

		$this->assertTrue($property->validate(array()));
		$this->assertTrue($property->validate(array('foo')));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidFormat()
	{
		$property = new ArrayType('test');

		$this->assertTrue($property->validate('foo'));
	}

	public function testValidateNull()
	{
		$property = new ArrayType('test');

		$this->assertTrue($property->validate(null));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateNullIsRequired()
	{
		$property = new ArrayType('test');
		$property->setRequired(true);

		$this->assertTrue($property->validate(null));
		$this->assertTrue($property->isRequired());
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateMinLength()
	{
		$property = new ArrayType('test');
		$property->setMinLength(1);

		$this->assertTrue($property->validate(array()));
		$this->assertEquals(1, $property->getMinLength());
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateMaxLength()
	{
		$property = new ArrayType('test');
		$property->setMaxLength(1);

		$this->assertTrue($property->validate(array('foo', 'bar')));
		$this->assertEquals(1, $property->getMaxLength());
	}

	public function testSetPrototype()
	{
		$prototype = new String('foo');

		$property = new ArrayType('test');
		$property->setPrototype($prototype);

		$this->assertEquals($prototype, $property->getPrototype());
	}

	public function testGetId()
	{
		$property = new ArrayType('test');

		$this->assertEquals('79db27401a9ee86ee36d6c38ba1cd653', $property->getId());

		$property = new ArrayType('test');
		$property->setPrototype(new String('foo'));

		$this->assertEquals('22f80b3e7b33b9430847a410fb4eea55', $property->getId());
	}
}
