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

use PSX\Framework\Test\ControllerTestCase;
use PSX\Json\Parser;

/**
 * ControllerAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerAbstractTest extends ControllerTestCase
{
    public function testNormalRequest()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller', 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals('foobar', $body, $body);
    }

    public function testInnerApi()
    {
        $data = json_encode(array(
            'foo' => 'bar',
            'bar' => array('foo' => 'nested'),
            'entries' => array(array('title' => 'bar'), array('title' => 'foo')),
        ));

        $response = $this->sendRequest('http://127.0.0.1/controller/inspect?foo=bar', 'POST', [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json'
        ], $data);

        $body = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString('{"bar": "foo"}', $body, $body);
    }

    public function testForward()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/forward', 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString('{"foo": "bar"}', $body, $body);
    }

    public function testForwardInvalid()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/forward_invalid', 'GET');
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body, true);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertEquals(false, $data['success']);
        $this->assertEquals('RuntimeException', $data['title']);
        $this->assertEquals('Could not find route for source Foo\Bar', substr($data['message'], 0, 39));
    }

    public function testRedirect()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/redirect', 'GET');

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('/redirect/bar', substr($response->getHeader('Location'), -13));
    }

    public function testRedirectAbsoluteString()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/absolute/string', 'GET');

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('http://localhost.com/foobar', $response->getHeader('Location'));
    }

    public function testRedirectAbsoluteObject()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/absolute/object', 'GET');

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('http://localhost.com/foobar', $response->getHeader('Location'));
    }

    public function testSetArrayBody()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/array', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{"foo":["bar"]}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testSetStdClassBody()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/stdClass', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{"foo":["bar"]}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testSetRecordBody()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/record', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{"foo":["bar"]}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testSetDomDocumentBody()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/dom', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<XML
<?xml version="1.0"?>
<foo>bar</foo>
XML;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    public function testSetSimpleXmlBody()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/simplexml', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<XML
<?xml version="1.0"?>
<foo>bar</foo>
XML;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    public function testSetStringBody()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/string', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<TEXT
foobar
TEXT;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals($expect, $body, $body);
    }

    public function testSetStreamBody()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/file', 'GET');
        $body     = $response->getBody();

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertInstanceOf('PSX\Http\Stream\FileStream', $body);
        $this->assertEquals('foo.txt', $body->getFileName());
        $this->assertEquals('application/octet-stream', $body->getContentType());

        $expect = <<<TEXT
foobar
TEXT;

        $this->assertEquals($expect, (string) $body);
    }

    /**
     * In case the controller calls the setBody method multiple times only the
     * first call gets written as response since the response gets appendend
     * which would probably produce an invalid output
     */
    public function testSetDoubleBody()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/double_body', 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals('foo', $body, $body);
    }

    /**
     * @dataProvider requestMethodProvider
     */
    public function testAllRequestMethods($requestMethod)
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/methods', $requestMethod);
        $body     = (string) $response->getBody();

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals('foobar', $body, $body);
    }

    public function requestMethodProvider()
    {
        return array(
            ['DELETE'],
            ['GET'],
            ['HEAD'],
            ['OPTIONS'],
            ['POST'],
            ['PUT'],
            ['TRACE'],
            ['PROPFIND'],
        );
    }

    public function testUnknownLocation()
    {
        $response = $this->sendRequest('http://127.0.0.1/controller/foobar', 'GET');
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body, true);

        $this->assertEquals(404, $response->getStatusCode(), $body);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertEquals(false, $data['success']);
        $this->assertEquals('PSX\Framework\Loader\InvalidPathException', $data['title']);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/controller', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doIndex'],
            [['POST'], '/controller/inspect', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doInspect'],
            [['GET'], '/controller/forward', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doForward'],
            [['GET'], '/controller/forward_invalid', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doForwardInvalidRoute'],
            [['GET'], '/controller/redirect', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doRedirect'],
            [['GET'], '/controller/absolute/string', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doRedirectAbsoluteString'],
            [['GET'], '/controller/absolute/object', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doRedirectAbsoluteObject'],
            [['GET'], '/controller/array', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doSetArrayBody'],
            [['GET'], '/controller/stdClass', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doSetStdClassBody'],
            [['GET'], '/controller/record', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doSetRecordBody'],
            [['GET'], '/controller/dom', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doSetDomDocumentBody'],
            [['GET'], '/controller/simplexml', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doSetSimpleXmlBody'],
            [['GET'], '/controller/string', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doSetStringBody'],
            [['GET'], '/controller/file', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doSetStreamBody'],
            [['GET'], '/controller/double_body', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doSetDoubleBody'],
            [['GET'], '/redirect/:foo', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doRedirectDestiniation'],
            [['GET'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\TestApiController::doIndex'],
            [['GET'], '/api/insert', 'PSX\Framework\Tests\Controller\Foo\Application\TestApiController::doInsert'],
            [['GET'], '/api/inspect', 'PSX\Framework\Tests\Controller\Foo\Application\TestApiController::doInspect'],
            [['DELETE','GET','HEAD','OPTIONS','POST','PUT','TRACE','PROPFIND'], '/controller/methods', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doIndex'],
        );
    }
}
