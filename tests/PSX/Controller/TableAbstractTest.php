<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
			'message' => 'Record successful created',
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
			'message' => 'Record successful updated',
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
			'message' => 'Record successful deleted',
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
