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
 * BooleanTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BooleanTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$property = new Boolean('test');

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
		$property = new Boolean('test');

		$this->assertTrue($property->validate('foo'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidInteger()
	{
		$property = new Boolean('test');

		$this->assertTrue($property->validate(4));
	}

	public function testGetId()
	{
		$property = new Boolean('test');

		$this->assertEquals('b68b84b0d51610192c0f73a5561495b9', $property->getId());
	}
}
