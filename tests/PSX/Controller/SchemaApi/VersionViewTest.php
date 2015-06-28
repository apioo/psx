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

use PSX\Data\Record;
use PSX\Data\Writer;
use PSX\Data\WriterInterface;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Json;
use PSX\Test\ControllerDbTestCase;
use PSX\Url;

/**
 * VersionViewTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
		$response = $this->sendRequest('http://127.0.0.1/api', 'GET');
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{"entry": [
    {
      "id": 4,
      "userId": 3,
      "title": "blub",
      "date": "2013-04-29T16:56:32Z"
    },
    {
      "id": 3,
      "userId": 2,
      "title": "test",
      "date": "2013-04-29T16:56:32Z"
    },
    {
      "id": 2,
      "userId": 1,
      "title": "bar",
      "date": "2013-04-29T16:56:32Z"
    },
    {
      "id": 1,
      "userId": 1,
      "title": "foo",
      "date": "2013-04-29T16:56:32Z"
    }
  ]}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testPostNoVersion()
	{
		$data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32Z'));
		$response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Content-Type' => 'application/json'], $data);
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "You have successful create a record"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testPutNoVersion()
	{
		$data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
		$response = $this->sendRequest('http://127.0.0.1/api', 'PUT', ['Content-Type' => 'application/json'], $data);
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "You have successful update a record"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testDeleteNoVersion()
	{
		$data     = json_encode(array('id' => 1));
		$response = $this->sendRequest('http://127.0.0.1/api', 'DELETE', ['Content-Type' => 'application/json'], $data);
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "You have successful delete a record"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testGetClosedVersion()
	{
		$response = $this->sendRequest('http://127.0.0.1/api', 'GET', ['Accept' => 'application/vnd.psx.v1+json']);
		$body     = Json::decode((string) $response->getBody());

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
		$data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32Z'));
		$response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Accept' => 'application/vnd.psx.v1+json', 'Content-Type' => 'application/json'], $data);
		$body     = Json::decode((string) $response->getBody());

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
		$response = $this->sendRequest('http://127.0.0.1/api', 'PUT', ['Accept' => 'application/vnd.psx.v1+json', 'Content-Type' => 'application/json'], $data);
		$body     = Json::decode((string) $response->getBody());

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
		$response = $this->sendRequest('http://127.0.0.1/api', 'DELETE', ['Accept' => 'application/vnd.psx.v1+json', 'Content-Type' => 'application/json'], $data);
		$body     = Json::decode((string) $response->getBody());

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
		$response = $this->sendRequest('http://127.0.0.1/api', 'GET', ['Accept' => 'application/vnd.psx.v2+json']);
		$body     = (string) $response->getBody();

		$this->assertEquals('199 PSX "Version v2 is deprecated"', $response->getHeader('Warning'));

		$expect = <<<JSON
{"entry": [
    {
      "id": 4,
      "userId": 3,
      "title": "blub",
      "date": "2013-04-29T16:56:32Z"
    },
    {
      "id": 3,
      "userId": 2,
      "title": "test",
      "date": "2013-04-29T16:56:32Z"
    },
    {
      "id": 2,
      "userId": 1,
      "title": "bar",
      "date": "2013-04-29T16:56:32Z"
    },
    {
      "id": 1,
      "userId": 1,
      "title": "foo",
      "date": "2013-04-29T16:56:32Z"
    }
  ]}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testPostDeprecatedVersion()
	{
		$data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32Z'));
		$response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Accept' => 'application/vnd.psx.v2+json', 'Content-Type' => 'application/json'], $data);
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "You have successful create a record"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertEquals('199 PSX "Version v2 is deprecated"', $response->getHeader('Warning'));
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testPutDeprecatedVersion()
	{
		$data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
		$response = $this->sendRequest('http://127.0.0.1/api', 'PUT', ['Accept' => 'application/vnd.psx.v2+json', 'Content-Type' => 'application/json'], $data);
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "You have successful update a record"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertEquals('199 PSX "Version v2 is deprecated"', $response->getHeader('Warning'));
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testDeleteDeprecatedVersion()
	{
		$data     = json_encode(array('id' => 1));
		$response = $this->sendRequest('http://127.0.0.1/api', 'DELETE', ['Accept' => 'application/vnd.psx.v2+json', 'Content-Type' => 'application/json'], $data);
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "You have successful delete a record"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertEquals('199 PSX "Version v2 is deprecated"', $response->getHeader('Warning'));
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testGetActiveVersion()
	{
		$response = $this->sendRequest('http://127.0.0.1/api', 'GET', ['Accept' => 'application/vnd.psx.v3+json']);
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{"entry": [
    {
      "id": 4,
      "userId": 3,
      "title": "blub",
      "date": "2013-04-29T16:56:32Z"
    },
    {
      "id": 3,
      "userId": 2,
      "title": "test",
      "date": "2013-04-29T16:56:32Z"
    },
    {
      "id": 2,
      "userId": 1,
      "title": "bar",
      "date": "2013-04-29T16:56:32Z"
    },
    {
      "id": 1,
      "userId": 1,
      "title": "foo",
      "date": "2013-04-29T16:56:32Z"
    }
  ]}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testPostActiveVersion()
	{
		$data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32Z'));
		$response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Accept' => 'application/vnd.psx.v3+json', 'Content-Type' => 'application/json'], $data);
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "You have successful create a record"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testPutActiveVersion()
	{
		$data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
		$response = $this->sendRequest('http://127.0.0.1/api', 'PUT', ['Accept' => 'application/vnd.psx.v3+json', 'Content-Type' => 'application/json'], $data);
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "You have successful update a record"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testDeleteActiveVersion()
	{
		$data     = json_encode(array('id' => 1));
		$response = $this->sendRequest('http://127.0.0.1/api', 'DELETE', ['Accept' => 'application/vnd.psx.v3+json', 'Content-Type' => 'application/json'], $data);
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{
	"success": true,
	"message": "You have successful delete a record"
}
JSON;

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testGetUnknownVersion()
	{
		$response = $this->sendRequest('http://127.0.0.1/api', 'GET', ['Accept' => 'application/vnd.psx.v4+json']);
		$body     = Json::decode((string) $response->getBody());

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
		$data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32Z'));
		$response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Accept' => 'application/vnd.psx.v4+json', 'Content-Type' => 'application/json'], $data);
		$body     = Json::decode((string) $response->getBody());

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
		$response = $this->sendRequest('http://127.0.0.1/api', 'PUT', ['Accept' => 'application/vnd.psx.v4+json', 'Content-Type' => 'application/json'], $data);
		$body     = Json::decode((string) $response->getBody());

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
		$response = $this->sendRequest('http://127.0.0.1/api', 'DELETE', ['Accept' => 'application/vnd.psx.v4+json', 'Content-Type' => 'application/json'], $data);
		$body     = Json::decode((string) $response->getBody());

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
