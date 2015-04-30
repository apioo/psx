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

use PSX\Url;

/**
 * StringTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StringTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$property = new String('test');

		$this->assertTrue($property->validate('foo'));
	}

	public function testValidateObjectToString()
	{
		$property = new String('test');
		$property->setMinLength(3);

		$property->validate(new Url('http://google.com'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidFormat()
	{
		$property = new String('test');
		$property->validate(new \stdClass());
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidFormatArray()
	{
		$property = new String('test');
		$property->validate(array());
	}

	public function testMinLength()
	{
		$property = new String('test');
		$property->setMinLength(3);

		$this->assertTrue($property->validate('foo'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testMinLengthInvalid()
	{
		$property = new String('test');
		$property->setMinLength(3);

		$property->validate('fo');
	}

	public function testMaxLength()
	{
		$property = new String('test');
		$property->setMaxLength(3);

		$this->assertTrue($property->validate('foo'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testMaxLengthInvalid()
	{
		$property = new String('test');
		$property->setMaxLength(3);

		$property->validate('fooo');
	}

	public function testPattern()
	{
		$property = new String('test');
		$property->setPattern('[A-z]+');

		$this->assertTrue($property->validate('foo'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testPatternInvalid()
	{
		$property = new String('test');
		$property->setPattern('[A-z]');

		$property->validate('123');
	}

	public function testEnumeration()
	{
		$property = new String('test');
		$property->setEnumeration(array('foo', 'bar'));

		$this->assertTrue($property->validate('foo'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testEnumerationInvalid()
	{
		$property = new String('test');
		$property->setEnumeration(array('foo', 'bar'));

		$property->validate('test');
	}

	public function testValidateNull()
	{
		$property = new String('test');

		$this->assertTrue($property->validate(null));
	}
	
	public function testGetId()
	{
		$property = new String('test');

		$this->assertEquals('4e089d32782b7d5d081230e97be69745', $property->getId());
	}
}
