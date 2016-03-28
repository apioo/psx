<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Framework\Tests\Controller;

use PSX\Framework\Test\ControllerDbTestCase;
use PSX\Framework\Test\Environment;

/**
 * SchemaApiAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SchemaApiAbstractTest extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/../../../Sql/Tests/table_fixture.xml');
    }

    public function testGet()
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

    public function testPost()
    {
        $data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32Z'));
        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Content-Type' => 'application/json'], $data);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
	"success": true,
	"message": "You have successful post a record"
}
JSON;

        $this->assertEquals(201, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testPostInvalidTitleLength()
    {
        Environment::getService('config')->set('psx_debug', false);

        $data     = json_encode(array('userId' => 3, 'title' => 'foobarfoobarfoobarfoobar', 'date' => '2013-05-29T16:56:32Z'));
        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Content-Type' => 'application/json'], $data);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
	"success": false,
	"message": "/title must contain less then 16 characters",
	"title": "Internal Server Error"
}
JSON;

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testPostInvalidFields()
    {
        Environment::getService('config')->set('psx_debug', false);

        $data     = json_encode(array('foobar' => 'title'));
        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Content-Type' => 'application/json'], $data);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
	"success": false,
	"message": "/title is required",
	"title": "Internal Server Error"
}
JSON;

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testPut()
    {
        $data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
        $response = $this->sendRequest('http://127.0.0.1/api', 'PUT', ['Content-Type' => 'application/json'], $data);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
	"success": true,
	"message": "You have successful put a record"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testDelete()
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

    public function testPatch()
    {
        $data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
        $response = $this->sendRequest('http://127.0.0.1/api', 'PATCH', ['Content-Type' => 'application/json'], $data);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "success": true,
    "message": "You have successful patch a record"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    protected function getPaths()
    {
        return array(
            [['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiController'],
        );
    }
}
