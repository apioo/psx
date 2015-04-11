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

use PSX\Data\Schema;
use PSX\Data\Schema\Property;

/**
 * FactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testMethod()
	{
		$method = Factory::getMethod('POST');
		$method->setDescription('foobar');
		$method->addQueryParameter(new Property\String('foo'));
		$method->setRequest(new Schema(new Property\String('foo')));
		$method->addResponse(200, new Schema(new Property\String('foo')));

		$this->assertEquals('foobar', $method->getDescription());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $method->getQueryParameters());
		$this->assertTrue($method->hasRequest());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $method->getRequest());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $method->getResponse(200));
		$this->assertTrue($method->hasResponse(200));
		$this->assertFalse($method->hasResponse(201));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testGetResponseInvalid()
	{
		Factory::getMethod('POST')->getResponse(500);
	}
}
