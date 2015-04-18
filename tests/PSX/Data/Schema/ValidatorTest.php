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

namespace PSX\Data\Schema;

use PSX\Data\Schema\Generator\TestSchema;
use PSX\Test\Environment;

/**
 * ValidatorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$json = <<<'JSON'
{
	"tags": ["foo"],
	"receiver": [{
		"title": "bar"
	}],
	"read": true,
	"author": {
		"title": "test"
	},
	"sendDate": "2014-07-22",
	"readDate": "2014-07-22T22:47:00",
	"expires": "P1M",
	"price": 13.37,
	"rating": 4,
	"content": "foobar",
	"question": "foo",
	"coffeeTime": "16:00:00"
}
JSON;

		$data = json_decode($json);

		$validator = new Validator();
		$schema    = Environment::getService('schema_manager')->getSchema('PSX\Data\Schema\Generator\TestSchema');

		$this->assertTrue($validator->validate($schema, $data));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 * @expectedExceptionMessage Data object expected at $.author
	 */
	public function testValidateInvalidObject()
	{
		$json = <<<'JSON'
{
	"tags": ["foo"],
	"receiver": [{
		"title": "bar"
	}],
	"read": true,
	"author": [],
	"sendDate": "2014-07-22",
	"readDate": "2014-07-22T22:47:00",
	"expires": "P1M",
	"price": 13.37,
	"rating": 4,
	"content": "foobar",
	"question": "foo",
	"coffeeTime": "16:00:00"
}
JSON;

		$data = json_decode($json);

		$validator = new Validator();
		$schema    = Environment::getService('schema_manager')->getSchema('PSX\Data\Schema\Generator\TestSchema');

		$this->assertTrue($validator->validate($schema, $data));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 * @expectedExceptionMessage Required property $.author.title not available
	 */
	public function testValidateInvalidObjectKey()
	{
		$json = <<<'JSON'
{
	"tags": ["foo"],
	"receiver": [{
		"title": "bar"
	}],
	"read": true,
	"author": {},
	"sendDate": "2014-07-22",
	"readDate": "2014-07-22T22:47:00",
	"expires": "P1M",
	"price": 13.37,
	"rating": 4,
	"content": "foobar",
	"question": "foo",
	"coffeeTime": "16:00:00"
}
JSON;

		$data = json_decode($json);

		$validator = new Validator();
		$schema    = Environment::getService('schema_manager')->getSchema('PSX\Data\Schema\Generator\TestSchema');

		$this->assertTrue($validator->validate($schema, $data));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 * @expectedExceptionMessage Data array expected at $.receiver
	 */
	public function testValidateInvalidArray()
	{
		$json = <<<'JSON'
{
	"tags": ["foo"],
	"receiver": "foo",
	"read": true,
	"author": [],
	"sendDate": "2014-07-22",
	"readDate": "2014-07-22T22:47:00",
	"expires": "P1M",
	"price": 13.37,
	"rating": 4,
	"content": "foobar",
	"question": "foo",
	"coffeeTime": "16:00:00"
}
JSON;

		$data = json_decode($json);

		$validator = new Validator();
		$schema    = Environment::getService('schema_manager')->getSchema('PSX\Data\Schema\Generator\TestSchema');

		$this->assertTrue($validator->validate($schema, $data));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 * @expectedExceptionMessage Data object expected at $.receiver[0]
	 */
	public function testValidateInvalidArrayKey()
	{
		$json = <<<'JSON'
{
	"tags": ["foo"],
	"receiver": ["foo"],
	"read": true,
	"author": [],
	"sendDate": "2014-07-22",
	"readDate": "2014-07-22T22:47:00",
	"expires": "P1M",
	"price": 13.37,
	"rating": 4,
	"content": "foobar",
	"question": "foo",
	"coffeeTime": "16:00:00"
}
JSON;

		$data = json_decode($json);

		$validator = new Validator();
		$schema    = Environment::getService('schema_manager')->getSchema('PSX\Data\Schema\Generator\TestSchema');

		$this->assertTrue($validator->validate($schema, $data));
	}
}
