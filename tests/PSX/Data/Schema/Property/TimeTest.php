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
 * TimeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TimeTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$property = new Time('test');

		$this->assertTrue($property->validate('10:00:00'));
		$this->assertTrue($property->validate('10:00:00+02:00'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidFormat()
	{
		$property = new Time('test');

		$this->assertTrue($property->validate('foo'));
	}

	public function testValidateNull()
	{
		$property = new Time('test');

		$this->assertTrue($property->validate(null));
	}

	public function testValidateDateTime()
	{
		$property = new Time('test');

		$this->assertTrue($property->validate(new \DateTime()));
	}

	public function testGetId()
	{
		$property = new Time('test');

		$this->assertEquals('3c6aef662b76a50263a19c4f3589414d', $property->getId());
	}
}
