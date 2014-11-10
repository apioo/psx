<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Controller\SchemaApi;

use PSX\Data\Writer;
use PSX\Data\WriterInterface;
use PSX\Data\Record;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Json;
use PSX\Loader\Location;
use PSX\Test\ControllerDbTestCase;
use PSX\Url;

/**
 * NoViewTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class NoViewTest extends ControllerDbTestCase
{
	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(__DIR__ . '/../../Sql/table_fixture.xml');
	}

	public function testGet()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(405, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Method is not allowed', substr($body['message'], 0, 21));
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

		$this->assertEquals(405, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Method is not allowed', substr($body['message'], 0, 21));
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

		$this->assertEquals(405, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Method is not allowed', substr($body['message'], 0, 21));
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

		$this->assertEquals(405, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Method is not allowed', substr($body['message'], 0, 21));
	}

	protected function getPaths()
	{
		return array(
			[['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Controller\Foo\Application\SchemaApi\NoViewController'],
		);
	}
}
