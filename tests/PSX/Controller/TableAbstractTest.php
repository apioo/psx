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
	"totalResults": 4,
	"entry": [{
		"id": 4,
		"userId": 3,
		"title": "blub",
		"date": "2013-04-29T16:56:32+00:00"
	},{
		"id": 3,
		"userId": 2,
		"title": "test",
		"date": "2013-04-29T16:56:32+00:00"
	},{
		"id": 2,
		"userId": 1,
		"title": "bar",
		"date": "2013-04-29T16:56:32+00:00"
	},{
		"id": 1,
		"userId": 1,
		"title": "foo",
		"date": "2013-04-29T16:56:32+00:00"
	}]
}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testPost()
	{
		$data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32+00:00'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'POST', array('Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$expect = array(
			'success' => true,
			'message' => 'Comment successful created',
		);

		$this->assertEquals($expect, $body);

		// check database
		$result = $this->connection->createQueryBuilder()
			->select(array('id', 'userId', 'title', 'date'))
			->from('psx_handler_comment')
			->execute()
			->fetchAll();

		$expect = array(
			array(
				'id' => '1',
				'userId' => '1',
				'title' => 'foo',
				'date' => '2013-04-29 16:56:32',
			),
			array(
				'id' => '2',
				'userId' => '1',
				'title' => 'bar',
				'date' => '2013-04-29 16:56:32',
			),
			array(
				'id' => '3',
				'userId' => '2',
				'title' => 'test',
				'date' => '2013-04-29 16:56:32',
			),
			array(
				'id' => '4',
				'userId' => '3',
				'title' => 'blub',
				'date' => '2013-04-29 16:56:32',
			),
			array(
				'id' => '5',
				'userId' => '3',
				'title' => 'test',
				'date' => '2013-05-29 16:56:32',
			),
		);

		$this->assertEquals($expect, $result);
	}

	public function testPut()
	{
		$data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'PUT', array('Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$expect = array(
			'success' => true,
			'message' => 'Comment successful updated',
		);

		$this->assertEquals($expect, $body);

		// check database
		$result = $this->connection->createQueryBuilder()
			->select(array('id', 'userId', 'title', 'date'))
			->from('psx_handler_comment')
			->execute()
			->fetchAll();

		$expect = array(
			array(
				'id' => '1',
				'userId' => '3',
				'title' => 'foobar',
				'date' => '2013-04-29 16:56:32',
			),
			array(
				'id' => '2',
				'userId' => '1',
				'title' => 'bar',
				'date' => '2013-04-29 16:56:32',
			),
			array(
				'id' => '3',
				'userId' => '2',
				'title' => 'test',
				'date' => '2013-04-29 16:56:32',
			),
			array(
				'id' => '4',
				'userId' => '3',
				'title' => 'blub',
				'date' => '2013-04-29 16:56:32',
			),
		);

		$this->assertEquals($expect, $result);
	}

	public function testDelete()
	{
		$data     = json_encode(array('id' => 1));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'DELETE', array('Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$expect = array(
			'success' => true,
			'message' => 'Comment successful deleted',
		);

		$this->assertEquals($expect, $body);

		// check database
		$result = $this->connection->createQueryBuilder()
			->select(array('id', 'userId', 'title', 'date'))
			->from('psx_handler_comment')
			->execute()
			->fetchAll();

		$expect = array(
			array(
				'id' => '2',
				'userId' => '1',
				'title' => 'bar',
				'date' => '2013-04-29 16:56:32',
			),
			array(
				'id' => '3',
				'userId' => '2',
				'title' => 'test',
				'date' => '2013-04-29 16:56:32',
			),
			array(
				'id' => '4',
				'userId' => '3',
				'title' => 'blub',
				'date' => '2013-04-29 16:56:32',
			),
		);

		$this->assertEquals($expect, $result);
	}

	protected function getPaths()
	{
		return array(
			[['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Controller\Foo\Application\TestTableApiController'],
		);
	}
}
