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

use PSX\Data\Record;
use PSX\Data\Writer;
use PSX\Framework\Test\ControllerTestCase;

/**
 * ApiAbstractValidateTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiAbstractValidateTest extends ControllerTestCase
{
    public function testIndex()
    {
        $response = $this->sendRequest('http://127.0.0.1/api', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "foo": "bar"
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testInsert()
    {
        $body     = json_encode(['title' => 'foo', 'author' => ['name' => 'bar']]);
        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Content-Type' => 'application/json'], $body);
        $body     = (string) $response->getBody();
        $body     = self::normalizeExceptionResponse($body);

        $expect = <<<'JSON'
{
    "success": true
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testInsertInvalidTitle()
    {
        $body     = json_encode(['title' => 'foofoofoo']);
        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Content-Type' => 'application/json'], $body);
        $body     = (string) $response->getBody();
        $body     = self::normalizeExceptionResponse($body);

        $expect = <<<'JSON'
{
    "success": false,
    "title": "PSX\\Validate\\ValidationException",
    "message": "/title has an invalid length min 3 and max 8 signs",
    "trace": "",
    "context": ""
}
JSON;

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testInsertInvalidAuthorName()
    {
        $body     = json_encode(['author' => ['name' => 'foofoofoo']]);
        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Content-Type' => 'application/json'], $body);
        $body     = (string) $response->getBody();
        $body     = self::normalizeExceptionResponse($body);

        $expect = <<<'JSON'
{
    "success": false,
    "title": "PSX\\Validate\\ValidationException",
    "message": "/author/name has an invalid length min 3 and max 8 signs",
    "trace": "",
    "context": ""
}
JSON;

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testInsertUnknownProperty()
    {
        $body     = json_encode(['title' => 'foo', 'foo' => 'bar']);
        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Content-Type' => 'application/json'], $body);
        $body     = (string) $response->getBody();
        $body     = self::normalizeExceptionResponse($body);

        $expect = <<<'JSON'
{
    "success": false,
    "title": "PSX\\Schema\\ValidationException",
    "message": "/ property \"foo\" does not exist",
    "trace": "",
    "context": ""
}
JSON;

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testInsertUnknownNestedProperty()
    {
        $body     = json_encode(['title' => 'foo', 'author' => ['name' => 'foo', 'foo' => 'bar']]);
        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Content-Type' => 'application/json'], $body);
        $body     = (string) $response->getBody();
        $body     = self::normalizeExceptionResponse($body);

        $expect = <<<'JSON'
{
    "success": false,
    "title": "PSX\\Schema\\ValidationException",
    "message": "/author property \"foo\" does not exist",
    "trace": "",
    "context": ""
}
JSON;

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\TestApiValidateController::doIndex'],
            [['POST'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\TestApiValidateController::doInsert'],
        );
    }
}
