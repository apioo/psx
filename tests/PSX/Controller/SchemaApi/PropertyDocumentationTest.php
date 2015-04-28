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

namespace PSX\Controller\SchemaApi;

use PSX\Data\Writer;
use PSX\Data\WriterInterface;
use PSX\Data\Record;
use PSX\DateTime;
use PSX\DateTime\Date;
use PSX\DateTime\Duration;
use PSX\DateTime\Time;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Json;
use PSX\Test\ControllerTestCase;
use PSX\Url;

/**
 * PropertyDocumentationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PropertyDocumentationTest extends ControllerTestCase
{
	/**
	 * @dataProvider providerTypes
	 */
	public function testGet($type)
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api?type=' . $type), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString(self::getExpected(), $body, $body);
	}

	public function testPost()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'POST', [], self::getExpected());
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString(self::getExpected(), $body, $body);
	}

	protected function getPaths()
	{
		return array(
			[['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Controller\Foo\Application\SchemaApi\PropertyDocumentationController'],
		);
	}

	public function providerTypes()
	{
		return [
			[1],
			[2],
			[3],
		];
	}

	public static function getDataByType($type)
	{
		switch($type)
		{
			case 1:
				// we return actual types
				return array(
					'array' => ['bar'],
					'boolean' => true,
					'complex' => [
						'foo' => 'bar'
					],
					'date' => new Date(2015, 5, 1),
					'dateTime' => new DateTime(2015, 5, 1, 13, 37, 14),
					'duration' => new Duration('P1M'),
					'float' => 13.37,
					'integer' => 7,
					'string' => 'bar',
					'time' => new Time(13, 37, 14),
				);
				break;

			case 2:
				// we return only strings like from an database
				return array(
					'array' => ['bar'],
					'boolean' => 'true',
					'complex' => [
						'foo' => 'bar'
					],
					'date' => '2015-05-01',
					'dateTime' => '2015-05-01T13:37:14Z',
					'duration' => 'P1M',
					'float' => '13.37',
					'integer' => '7',
					'string' => 'bar',
					'time' => '13:37:14',
				);
				break;

			case 3:
				// we return types which we get from the doctrine mapper
				return array(
					'array' => ['bar'],
					'boolean' => true,
					'complex' => [
						'foo' => 'bar'
					],
					'date' => new \DateTime('2015-05-01T13:37:14Z'),
					'dateTime' => new \DateTime('2015-05-01T13:37:14Z'),
					'duration' => 'P1M',
					'float' => 13.37,
					'integer' => 7,
					'string' => 'bar',
					'time' => new \DateTime('2015-05-01T13:37:14Z'),
				);
				break;
		}
	}

	public static function getExpected()
	{
		return <<<JSON
{
    "array": [
        "bar"
    ],
    "boolean": true,
    "complex": {
        "foo": "bar"
    },
    "date": "2015-05-01",
    "dateTime": "2015-05-01T13:37:14Z",
    "duration": "P1M",
    "float": 13.37,
    "integer": 7,
    "string": "bar",
    "time": "13:37:14"
}
JSON;
	}
}
