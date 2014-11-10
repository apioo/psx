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
 * VersionViewTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class VersionViewTest extends ControllerDbTestCase
{
	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(__DIR__ . '/../../Sql/table_fixture.xml');
	}

	public function testGetNoVersion()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(415, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Requires an Accept header containing an explicit version', substr($body['message'], 0, 56));
	}

	public function testPostNoVersion()
	{
		$data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32+00:00'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'POST', array('Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(415, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Requires an Accept header containing an explicit version', substr($body['message'], 0, 56));
	}

	public function testPutNoVersion()
	{
		$data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'PUT', array('Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(415, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Requires an Accept header containing an explicit version', substr($body['message'], 0, 56));
	}

	public function testDeleteNoVersion()
	{
		$data     = json_encode(array('id' => 1));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'DELETE', array('Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(415, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Requires an Accept header containing an explicit version', substr($body['message'], 0, 56));
	}

	public function testGetClosedVersion()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'GET', array('Accept' => 'application/vnd.psx.v1+json'));
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(410, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Version v1 is not longer supported', substr($body['message'], 0, 34));
	}

	public function testPostClosedVersion()
	{
		$data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32+00:00'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'POST', array('Accept' => 'application/vnd.psx.v1+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(410, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Version v1 is not longer supported', substr($body['message'], 0, 34));
	}

	public function testPutClosedVersion()
	{
		$data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'PUT', array('Accept' => 'application/vnd.psx.v1+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(410, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Version v1 is not longer supported', substr($body['message'], 0, 34));
	}

	public function testDeleteClosedVersion()
	{
		$data     = json_encode(array('id' => 1));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'DELETE', array('Accept' => 'application/vnd.psx.v1+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(410, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Version v1 is not longer supported', substr($body['message'], 0, 34));
	}

	public function testGetDeprecatedVersion()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'GET', array('Accept' => 'application/vnd.psx.v2+json'));
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$this->assertEquals('199 PSX "Version v2 is deprecated"', $response->getHeader('Warning'));

		$expect = <<<JSON
{"entry": [
    {
      "id": 4,
      "userId": 3,
      "title": "blub",
      "date": "2013-04-29T16:56:32+00:00"
    },
    {
      "id": 3,
      "userId": 2,
      "title": "test",
      "date": "2013-04-29T16:56:32+00:00"
    },
    {
      "id": 2,
      "userId": 1,
      "title": "bar",
      "date": "2013-04-29T16:56:32+00:00"
    },
    {
      "id": 1,
      "userId": 1,
      "title": "foo",
      "date": "2013-04-29T16:56:32+00:00"
    }
  ]}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testPostDeprecatedVersion()
	{
		$data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32+00:00'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'POST', array('Accept' => 'application/vnd.psx.v2+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals('199 PSX "Version v2 is deprecated"', $response->getHeader('Warning'));

		$expect = array(
			'success' => true,
			'message' => 'You have successful create a record',
		);

		$this->assertEquals($expect, $body);
	}

	public function testPutDeprecatedVersion()
	{
		$data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'PUT', array('Accept' => 'application/vnd.psx.v2+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals('199 PSX "Version v2 is deprecated"', $response->getHeader('Warning'));

		$expect = array(
			'success' => true,
			'message' => 'You have successful update a record',
		);

		$this->assertEquals($expect, $body);
	}

	public function testDeleteDeprecatedVersion()
	{
		$data     = json_encode(array('id' => 1));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'DELETE', array('Accept' => 'application/vnd.psx.v2+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals('199 PSX "Version v2 is deprecated"', $response->getHeader('Warning'));

		$expect = array(
			'success' => true,
			'message' => 'You have successful delete a record',
		);

		$this->assertEquals($expect, $body);
	}

	public function testGetActiveVersion()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'GET', array('Accept' => 'application/vnd.psx.v3+json'));
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$expect = <<<JSON
{"entry": [
    {
      "id": 4,
      "userId": 3,
      "title": "blub",
      "date": "2013-04-29T16:56:32+00:00"
    },
    {
      "id": 3,
      "userId": 2,
      "title": "test",
      "date": "2013-04-29T16:56:32+00:00"
    },
    {
      "id": 2,
      "userId": 1,
      "title": "bar",
      "date": "2013-04-29T16:56:32+00:00"
    },
    {
      "id": 1,
      "userId": 1,
      "title": "foo",
      "date": "2013-04-29T16:56:32+00:00"
    }
  ]}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testPostActiveVersion()
	{
		$data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32+00:00'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'POST', array('Accept' => 'application/vnd.psx.v3+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$expect = array(
			'success' => true,
			'message' => 'You have successful create a record',
		);

		$this->assertEquals($expect, $body);
	}

	public function testPutActiveVersion()
	{
		$data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'PUT', array('Accept' => 'application/vnd.psx.v3+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$expect = array(
			'success' => true,
			'message' => 'You have successful update a record',
		);

		$this->assertEquals($expect, $body);
	}

	public function testDeleteActiveVersion()
	{
		$data     = json_encode(array('id' => 1));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'DELETE', array('Accept' => 'application/vnd.psx.v3+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$expect = array(
			'success' => true,
			'message' => 'You have successful delete a record',
		);

		$this->assertEquals($expect, $body);
	}

	public function testGetUnknownVersion()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'GET', array('Accept' => 'application/vnd.psx.v4+json'));
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(406, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Version is not available', substr($body['message'], 0, 24));
	}

	public function testPostUnknownVersion()
	{
		$data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32+00:00'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'POST', array('Accept' => 'application/vnd.psx.v4+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(406, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Version is not available', substr($body['message'], 0, 24));
	}

	public function testPutUnknownVersion()
	{
		$data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'PUT', array('Accept' => 'application/vnd.psx.v4+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(406, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Version is not available', substr($body['message'], 0, 24));
	}

	public function testDeleteUnknownVersion()
	{
		$data     = json_encode(array('id' => 1));
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'DELETE', array('Accept' => 'application/vnd.psx.v4+json', 'Content-Type' => 'application/json'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = Json::decode((string) $response->getBody());

		$this->assertEquals(406, $response->getStatusCode());

		$this->assertArrayHasKey('success', $body);
		$this->assertArrayHasKey('title', $body);
		$this->assertArrayHasKey('message', $body);
		$this->assertArrayHasKey('trace', $body);
		$this->assertArrayHasKey('context', $body);

		$this->assertEquals(false, $body['success']);
		$this->assertEquals('Version is not available', substr($body['message'], 0, 24));
	}

	protected function getPaths()
	{
		return array(
			[['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Controller\Foo\Application\SchemaApi\VersionViewController'],
		);
	}
}
