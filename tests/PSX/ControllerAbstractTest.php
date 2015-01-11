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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

		$this->assertEquals('foobar', (string) $response->getBody());
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

		$this->assertJsonStringEqualsJsonString(json_encode(array('bar' => 'foo')), (string) $response->getBody());
	}

	public function testForward()
	{
		$path     = '/controller/forward';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);

		$this->assertJsonStringEqualsJsonString(json_encode(array('foo' => 'bar')), (string) $response->getBody());
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

		$expect = <<<JSON
{"foo":["bar"]}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
	}

	public function testSetRecordBody()
	{
		$path     = '/controller/record';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);

		$expect = <<<JSON
{"foo":["bar"]}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
	}

	public function testSetDomDocumentBody()
	{
		$path     = '/controller/dom';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);

		$expect = <<<XML
<?xml version="1.0"?>
<foo>bar</foo>
XML;

		$this->assertXmlStringEqualsXmlString($expect, (string) $response->getBody());
	}

	public function testSetSimpleXmlBody()
	{
		$path     = '/controller/simplexml';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);

		$expect = <<<XML
<?xml version="1.0"?>
<foo>bar</foo>
XML;

		$this->assertXmlStringEqualsXmlString($expect, (string) $response->getBody());
	}

	public function testSetStringBody()
	{
		$path     = '/controller/string';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$controller = $this->loadController($request, $response);

		$expect = <<<XML
foobar
XML;

		$this->assertEquals($expect, (string) $response->getBody());
	}

	public function testSetStreamBody()
	{
		$path     = '/controller/file';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();

		$controller = $this->loadController($request, $response);

		$body = $response->getBody();

		$this->assertInstanceOf('PSX\Http\Stream\FileStream', $body);
		$this->assertEquals('foo.txt', $body->getFileName());
		$this->assertEquals('application/octet-stream', $body->getContentType());

		$expect = <<<XML
foobar
XML;

		$this->assertEquals($expect, (string) $response->getBody());
	}

	public function testSetInvalidBody()
	{
		$path     = '/controller/invalid_body';
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		$this->loadController($request, $response);

		$body     = (string) $response->getBody();
		$response = Json::decode($body);

		$this->assertArrayHasKey('success', $response);
		$this->assertArrayHasKey('title', $response);
		$this->assertArrayHasKey('message', $response);
		$this->assertEquals(false, $response['success']);
		$this->assertEquals('InvalidArgumentException', $response['title']);
		$this->assertEquals('Invalid data type', substr($response['message'], 0, 17));
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

		$this->assertEquals('foo', $body);
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

		$this->assertEquals('foobar', (string) $response->getBody());
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

		$data = Json::decode($response->getBody());

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
			[['GET'], '/controller/redirect', 'PSX\Controller\Foo\Application\TestController::doRedirect'],
			[['GET'], '/controller/absolute/string', 'PSX\Controller\Foo\Application\TestController::doRedirectAbsoluteString'],
			[['GET'], '/controller/absolute/object', 'PSX\Controller\Foo\Application\TestController::doRedirectAbsoluteObject'],
			[['GET'], '/controller/array', 'PSX\Controller\Foo\Application\TestController::doSetArrayBody'],
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
