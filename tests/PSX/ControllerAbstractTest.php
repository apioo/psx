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

namespace PSX;

use PSX\Dispatch\RedirectException;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Http\Stream\StringStream;
use PSX\Json;
use PSX\Test\ControllerTestCase;
use PSX\Url;

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
		$path     = '/controller';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$this->assertEquals(null, $response->getStatusCode(), $body);
		$this->assertEquals('foobar', $body, $body);
	}

	public function testInnerApi()
	{
		$data = array(
			'foo' => 'bar',
			'bar' => array('foo' => 'nested'),
			'entries' => array(array('title' => 'bar'), array('title' => 'foo')),
		);

		$path     = '/controller/inspect';
		$request  = new Request(new Url('http://127.0.0.1' . $path . '?foo=bar'), 'POST', array('Content-Type' => 'application/json', 'Accept' => 'application/json'));
		$request->setBody(new StringStream(json_encode($data)));
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString('{"bar": "foo"}', $body, $body);
	}

	public function testForward()
	{
		$path     = '/controller/forward';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$this->assertEquals(null, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString('{"foo": "bar"}', $body, $body);
	}

	public function testForwardInvalid()
	{
		$path     = '/controller/forward_invalid';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();
		$data       = Json::decode($body);

		$this->assertEquals(500, $response->getStatusCode(), $body);
		$this->assertArrayHasKey('success', $data);
		$this->assertArrayHasKey('title', $data);
		$this->assertEquals(false, $data['success']);
		$this->assertEquals('RuntimeException', $data['title']);
		$this->assertEquals('Could not find route for source Foo\Bar', substr($data['message'], 0, 39));
	}

	public function testRedirect()
	{
		$path     = '/controller/redirect';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);

		$this->assertEquals(307, $response->getStatusCode());
		$this->assertEquals('/redirect/bar', substr($response->getHeader('Location'), -13));
	}

	public function testRedirectAbsoluteString()
	{
		$path     = '/controller/absolute/string';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);

		$this->assertEquals(307, $response->getStatusCode());
		$this->assertEquals('http://localhost.com/foobar', $response->getHeader('Location'));
	}

	public function testRedirectAbsoluteObject()
	{
		$path     = '/controller/absolute/object';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);

		$this->assertEquals(307, $response->getStatusCode());
		$this->assertEquals('http://localhost.com/foobar', $response->getHeader('Location'));
	}

	public function testSetArrayBody()
	{
		$path     = '/controller/array';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$expect = <<<JSON
{"foo":["bar"]}
JSON;

		$this->assertEquals(null, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testSetStdClassBody()
	{
		$path     = '/controller/stdClass';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$expect = <<<JSON
{"foo":["bar"]}
JSON;

		$this->assertEquals(null, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testSetRecordBody()
	{
		$path     = '/controller/record';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$expect = <<<JSON
{"foo":["bar"]}
JSON;

		$this->assertEquals(null, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testSetDomDocumentBody()
	{
		$path     = '/controller/dom';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$expect = <<<XML
<?xml version="1.0"?>
<foo>bar</foo>
XML;

		$this->assertEquals(null, $response->getStatusCode(), $body);
		$this->assertXmlStringEqualsXmlString($expect, $body, $body);
	}

	public function testSetSimpleXmlBody()
	{
		$path     = '/controller/simplexml';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$expect = <<<XML
<?xml version="1.0"?>
<foo>bar</foo>
XML;

		$this->assertEquals(null, $response->getStatusCode(), $body);
		$this->assertXmlStringEqualsXmlString($expect, $body, $body);
	}

	public function testSetStringBody()
	{
		$path     = '/controller/string';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$expect = <<<XML
foobar
XML;

		$this->assertEquals(null, $response->getStatusCode(), $body);
		$this->assertEquals($expect, $body, $body);
	}

	public function testSetStreamBody()
	{
		$path     = '/controller/file';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();

		$controller = $this->loadController($request, $response);
		$body       = $response->getBody();

		$this->assertEquals(null, $response->getStatusCode(), $body);
		$this->assertInstanceOf('PSX\Http\Stream\FileStream', $body);
		$this->assertEquals('foo.txt', $body->getFileName());
		$this->assertEquals('application/octet-stream', $body->getContentType());

		$expect = <<<XML
foobar
XML;

		$this->assertEquals($expect, (string) $body);
	}

	public function testSetInvalidBody()
	{
		$path     = '/controller/invalid_body';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$this->loadController($request, $response);

		$body = (string) $response->getBody();
		$data = Json::decode($body);

		$this->assertEquals(500, $response->getStatusCode(), $body);
		$this->assertArrayHasKey('success', $data);
		$this->assertArrayHasKey('title', $data);
		$this->assertArrayHasKey('message', $data);
		$this->assertEquals(false, $data['success']);
		$this->assertEquals('InvalidArgumentException', $data['title']);
		$this->assertEquals('Invalid data type', substr($data['message'], 0, 17));
	}

	/**
	 * In case the controller calls the setBody method multiple times only the
	 * first call gets written as response since the response gets appendend
	 * which would probably produce an invalid output
	 */
	public function testSetDoubleBody()
	{
		$path     = '/controller/double_body';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$this->loadController($request, $response);

		$body = (string) $response->getBody();

		$this->assertEquals(null, $response->getStatusCode(), $body);
		$this->assertEquals('foo', $body, $body);
	}

	/**
	 * @dataProvider requestMethodProvider
	 */
	public function testAllRequestMethods($requestMethod)
	{
		$path     = '/controller/methods';
		$request  = new Request(new Url('http://127.0.0.1' . $path), $requestMethod);
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);

		$body = (string) $response->getBody();

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
		$path     = '/controller/foobar';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);

		$body = (string) $response->getBody();
		$data = Json::decode($body);

		$this->assertEquals(404, $response->getStatusCode(), $body);
		$this->assertArrayHasKey('success', $data);
		$this->assertArrayHasKey('title', $data);
		$this->assertEquals(false, $data['success']);
		$this->assertEquals('PSX\Loader\InvalidPathException', $data['title']);
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/controller', 'PSX\Controller\Foo\Application\TestController::doIndex'],
			[['POST'], '/controller/inspect', 'PSX\Controller\Foo\Application\TestController::doInspect'],
			[['GET'], '/controller/forward', 'PSX\Controller\Foo\Application\TestController::doForward'],
			[['GET'], '/controller/forward_invalid', 'PSX\Controller\Foo\Application\TestController::doForwardInvalidRoute'],
			[['GET'], '/controller/redirect', 'PSX\Controller\Foo\Application\TestController::doRedirect'],
			[['GET'], '/controller/absolute/string', 'PSX\Controller\Foo\Application\TestController::doRedirectAbsoluteString'],
			[['GET'], '/controller/absolute/object', 'PSX\Controller\Foo\Application\TestController::doRedirectAbsoluteObject'],
			[['GET'], '/controller/array', 'PSX\Controller\Foo\Application\TestController::doSetArrayBody'],
			[['GET'], '/controller/stdClass', 'PSX\Controller\Foo\Application\TestController::doSetStdClassBody'],
			[['GET'], '/controller/record', 'PSX\Controller\Foo\Application\TestController::doSetRecordBody'],
			[['GET'], '/controller/dom', 'PSX\Controller\Foo\Application\TestController::doSetDomDocumentBody'],
			[['GET'], '/controller/simplexml', 'PSX\Controller\Foo\Application\TestController::doSetSimpleXmlBody'],
			[['GET'], '/controller/string', 'PSX\Controller\Foo\Application\TestController::doSetStringBody'],
			[['GET'], '/controller/file', 'PSX\Controller\Foo\Application\TestController::doSetStreamBody'],
			[['GET'], '/controller/invalid_body', 'PSX\Controller\Foo\Application\TestController::doSetInvalidBody'],
			[['GET'], '/controller/double_body', 'PSX\Controller\Foo\Application\TestController::doSetDoubleBody'],
			[['GET'], '/redirect/:foo', 'PSX\Controller\Foo\Application\TestController::doRedirectDestiniation'],
			[['GET'], '/api', 'PSX\Controller\Foo\Application\TestApiController::doIndex'],
			[['GET'], '/api/insert', 'PSX\Controller\Foo\Application\TestApiController::doInsert'],
			[['GET'], '/api/inspect', 'PSX\Controller\Foo\Application\TestApiController::doInspect'],
			[['DELETE','GET','HEAD','OPTIONS','POST','PUT','TRACE','PROPFIND'], '/controller/methods', 'PSX\Controller\Foo\Application\TestController::doIndex'],
		);
	}
}
