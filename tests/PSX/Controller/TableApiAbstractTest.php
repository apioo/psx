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

namespace PSX\Controller;

use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Json;
use PSX\Test\ControllerDbTestCase;
use PSX\Url;

/**
 * TableApiAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TableApiAbstractTest extends ControllerDbTestCase
{
	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(__DIR__ . '/../Sql/table_fixture.xml');
	}

	public function testGet()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$expect = <<<JSON
{
	"startIndex": 0,
	"count": 16,
	"totalResults": 1,
	"entry": [{
		"id": 1,
        "col_bigint": "68719476735",
        "col_blob": "Zm9vYmFy",
        "col_boolean": true,
        "col_datetime": "2015-01-21T23:59:59Z",
        "col_datetimetz": "2015-01-21T23:59:59Z",
        "col_date": "2015-01-21",
        "col_decimal": 10,
        "col_float": 10.37,
        "col_integer": 2147483647,
        "col_smallint": 255,
        "col_text": "foobar",
        "col_time": "23:59:59",
        "col_string": "foobar"
	}]
}
JSON;

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testPost()
	{
		$data = json_encode(array(
			'col_bigint' => '68719476735',
			'col_blob' => 'foobar',
			'col_boolean' => 'true',
			'col_datetime' => '2015-01-21T23:59:59+00:00',
			'col_datetimetz' => '2015-01-21T23:59:59+01:00',
			'col_date' => '2015-01-21',
			'col_decimal' => '10',
			'col_float' => '10.37',
			'col_integer' => '2147483647',
			'col_smallint' => '255',
			'col_text' => 'foobar',
			'col_time' => '23:59:59',
			'col_string' => 'foobar',
		));

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'POST', array('Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "Test successful created"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);

		// check database
		$result = $this->connection->createQueryBuilder()
			->select(array('id', 'col_bigint', 'col_blob', 'col_boolean', 'col_datetime', 'col_datetimetz', 'col_date', 'col_decimal', 'col_float', 'col_integer', 'col_smallint', 'col_text', 'col_time', 'col_string'))
			->from('psx_table_command_test')
			->execute()
			->fetchAll();

		$expect = array(
			array(
				'id' => '1',
				'col_bigint' => '68719476735',
				'col_blob' => 'foobar',
				'col_boolean' => '1',
				'col_datetime' => '2015-01-21 23:59:59',
				'col_datetimetz' => '2015-01-21 23:59:59',
				'col_date' => '2015-01-21',
				'col_decimal' => '10',
				'col_float' => '10.37',
				'col_integer' => '2147483647',
				'col_smallint' => '255',
				'col_text' => 'foobar',
				'col_time' => '23:59:59',
				'col_string' => 'foobar',
			),
			array(
				'id' => '2',
				'col_bigint' => '68719476735',
				'col_blob' => 'foobar',
				'col_boolean' => '1',
				'col_datetime' => '2015-01-21 23:59:59',
				'col_datetimetz' => '2015-01-21 23:59:59',
				'col_date' => '2015-01-21',
				'col_decimal' => '10',
				'col_float' => '10.37',
				'col_integer' => '2147483647',
				'col_smallint' => '255',
				'col_text' => 'foobar',
				'col_time' => '23:59:59',
				'col_string' => 'foobar',
			),
		);

		$this->assertEquals($expect, $result);
	}

	public function testPut()
	{
		$data = json_encode(array(
			'id' => 1,
			'col_bigint' => '68719476735',
			'col_blob' => 'foobar',
			'col_boolean' => 'true',
			'col_datetime' => '2015-01-21T23:59:59+00:00',
			'col_datetimetz' => '2015-01-21T23:59:59+01:00',
			'col_date' => '2015-01-21',
			'col_decimal' => '10',
			'col_float' => '10.37',
			'col_integer' => '2147483647',
			'col_smallint' => '255',
			'col_text' => 'foobar',
			'col_time' => '23:59:59',
			'col_string' => 'foo',
		));

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'PUT', array('Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "Test successful updated"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);

		// check database
		$result = $this->connection->createQueryBuilder()
			->select(array('id', 'col_bigint', 'col_blob', 'col_boolean', 'col_datetime', 'col_datetimetz', 'col_date', 'col_decimal', 'col_float', 'col_integer', 'col_smallint', 'col_text', 'col_time', 'col_string'))
			->from('psx_table_command_test')
			->execute()
			->fetchAll();

		$expect = array(
			array(
				'id' => '1',
				'col_bigint' => '68719476735',
				'col_blob' => 'foobar',
				'col_boolean' => '1',
				'col_datetime' => '2015-01-21 23:59:59',
				'col_datetimetz' => '2015-01-21 23:59:59',
				'col_date' => '2015-01-21',
				'col_decimal' => '10',
				'col_float' => '10.37',
				'col_integer' => '2147483647',
				'col_smallint' => '255',
				'col_text' => 'foobar',
				'col_time' => '23:59:59',
				'col_string' => 'foo',
			),
		);

		$this->assertEquals($expect, $result);
	}

	public function testPutNotAvailable()
	{
		$data     = json_encode(array('id' => 12, 'title' => 'foobar'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'PUT', array('Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(404, $response->getStatusCode());
		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Record not found', substr($body['message'], 0, 16));
	}

	public function testDelete()
	{
		$data     = json_encode(array('id' => 1));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'DELETE', array('Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "Test successful deleted"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);

		// check database
		$result = $this->connection->createQueryBuilder()
			->select(array('id', 'col_bigint', 'col_blob', 'col_boolean', 'col_datetime', 'col_datetimetz', 'col_date', 'col_decimal', 'col_float', 'col_integer', 'col_smallint', 'col_text', 'col_time', 'col_string'))
			->from('psx_table_command_test')
			->execute()
			->fetchAll();

		$expect = array(
		);

		$this->assertEquals($expect, $result);
	}

	public function testDeleteNotAvailable()
	{
		$data     = json_encode(array('id' => 12));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'DELETE', array('Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(404, $response->getStatusCode());
		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Record not found', substr($body['message'], 0, 16));
	}

	protected function getPaths()
	{
		return array(
			[['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Controller\Foo\Application\TestTableApiController'],
		);
	}
}
