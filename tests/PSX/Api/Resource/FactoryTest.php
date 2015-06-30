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

namespace PSX\Api\Resource;

/**
 * FactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testGetMethod()
	{
		$this->assertInstanceOf('PSX\Api\Resource\Delete', Factory::getMethod('DELETE'));
		$this->assertEquals('DELETE', Factory::getMethod('DELETE')->getName());
		$this->assertInstanceOf('PSX\Api\Resource\Get', Factory::getMethod('GET'));
		$this->assertEquals('GET', Factory::getMethod('GET')->getName());
		$this->assertInstanceOf('PSX\Api\Resource\Patch', Factory::getMethod('PATCH'));
		$this->assertEquals('PATCH', Factory::getMethod('PATCH')->getName());
		$this->assertInstanceOf('PSX\Api\Resource\Post', Factory::getMethod('POST'));
		$this->assertEquals('POST', Factory::getMethod('POST')->getName());
		$this->assertInstanceOf('PSX\Api\Resource\Put', Factory::getMethod('PUT'));
		$this->assertEquals('PUT', Factory::getMethod('PUT')->getName());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetMethodInvalid()
	{
		Factory::getMethod('FOO');
	}
}
