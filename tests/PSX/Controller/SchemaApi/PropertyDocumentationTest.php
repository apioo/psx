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
use PSX\Data\RecordInterface;
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
	 * @dataProvider getDataTypes
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

	/**
	 * Checks whether the data we received as post is converted to the right 
	 * types
	 *
	 * @param PHPUnit_Framework_TestCase $testCase
	 * @param PSX\Data\RecordInterface $record
	 */
	public static function assertRecord(\PHPUnit_Framework_TestCase $testCase, RecordInterface $record)
	{
		$testCase->assertInternalType('array', $record->getArray());
		$testCase->assertEquals(['bar'], $record->getArray());
		$testCase->assertInternalType('array', $record->getArrayComplex());
		$testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getArrayComplex()[0]);
		$testCase->assertEquals(['foo' => 'bar'], $record->getArrayComplex()[0]->getRecordInfo()->getFields());
		$testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getArrayComplex()[1]);
		$testCase->assertEquals(['foo' => 'foo'], $record->getArrayComplex()[1]->getRecordInfo()->getFields());
		$testCase->assertInternalType('array', $record->getArrayChoice());
		$testCase->assertInternalType('boolean', $record->getBoolean());
		$testCase->assertEquals(true, $record->getBoolean());
		$testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getChoice());
		$testCase->assertEquals(['foo' => 'bar'], $record->getComplex()->getRecordInfo()->getFields());
		$testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getComplex());
		$testCase->assertEquals(['foo' => 'bar'], $record->getComplex()->getRecordInfo()->getFields());
		$testCase->assertInstanceOf('PSX\DateTime\Date', $record->getDate());
		$testCase->assertEquals('2015-05-01', $record->getDate()->format('Y-m-d'));
		$testCase->assertInstanceOf('PSX\DateTime', $record->getDateTime());
		$testCase->assertEquals('2015-05-01T13:37:14Z', $record->getDateTime()->format('Y-m-d\TH:i:s\Z'));
		$testCase->assertInstanceOf('PSX\DateTime\Duration', $record->getDuration());
		$testCase->assertEquals('000100000000', $record->getDuration()->format('%Y%M%D%H%I%S'));
		$testCase->assertInternalType('float', $record->getFloat());
		$testCase->assertEquals(13.37, $record->getFloat());
		$testCase->assertInternalType('integer', $record->getInteger());
		$testCase->assertEquals(7, $record->getInteger());
		$testCase->assertInternalType('string', $record->getString());
		$testCase->assertEquals('bar', $record->getString());
		$testCase->assertInstanceOf('PSX\DateTime\Time', $record->getTime());
		$testCase->assertEquals('13:37:14', $record->getTime()->format('H:i:s'));
	}

	/**
	 * Returns all available data types which can be used as data provider
	 * 
	 * @return array
	 */
	public static function getDataTypes()
	{
		return [
			[1],
			[2],
			[3],
		];
	}

	/**
	 * Returns different response. The assimilator should convert all these 
	 * types to the same response format
	 *
	 * @param integer $type
	 * @return array
	 */
	public static function getDataByType($type)
	{
		switch($type)
		{
			case 1:
				// we return actual types
				return array(
					'array' => ['bar'],
					'arrayComplex' => [[
						'foo' => 'bar'
					],[
						'foo' => 'foo'
					]],
					'arrayChoice' => [[
						'foo' => 'baz'
					],[
						'bar' => 'bar'
					],[
						'foo' => 'foo'
					]],
					'boolean' => true,
					'choice' => [
						'bar' => 'test'
					],
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
					'arrayComplex' => [[
						'foo' => 'bar'
					],[
						'foo' => 'foo'
					]],
					'arrayChoice' => [[
						'foo' => 'baz'
					],[
						'bar' => 'bar'
					],[
						'foo' => 'foo'
					]],
					'boolean' => 'true',
					'choice' => [
						'bar' => 'test'
					],
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
					'arrayComplex' => [[
						'foo' => 'bar'
					],[
						'foo' => 'foo'
					]],
					'arrayChoice' => [[
						'foo' => 'baz'
					],[
						'bar' => 'bar'
					],[
						'foo' => 'foo'
					]],
					'boolean' => true,
					'choice' => [
						'bar' => 'test'
					],
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

	/**
	 * The JSON format which we expect as response
	 *
	 * @return string
	 */
	public static function getExpected()
	{
		return <<<JSON
{
    "array": [
        "bar"
    ],
    "arrayComplex": [{
        "foo": "bar"
    },{
        "foo": "foo"
    }],
    "arrayChoice": [{
        "foo": "baz"
    },{
        "bar": "bar"
    },{
        "foo": "foo"
    }],
    "boolean": true,
    "choice": {
        "bar": "test"
    },
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
