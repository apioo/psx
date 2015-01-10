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

use PSX\Http\Stream\TempStream;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Json;
use PSX\Loader\Location;
use PSX\Loader\LocationFinder\CallbackMethod;
use PSX\Loader\InvalidPathException;
use PSX\Test\ControllerTestCase;
use PSX\Url;

/**
 * ErrorControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ErrorControllerTest extends ControllerTestCase
{
	public function testExceptionDebug()
	{
		getContainer()->get('config')->set('psx_debug', true);

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/error'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$data       = Json::decode((string) $body);

		$this->assertEquals(false, $data['success']);
		$this->assertEquals('foo in', substr($data['message'], 0, 6));
	}

	public function testExceptionNoDebug()
	{
		getContainer()->get('config')->set('psx_debug', false);

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/error'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$data       = Json::decode((string) $body);

		$this->assertEquals(false, $data['success']);
		$this->assertEquals('The server encountered an internal error and was unable to complete your request.', $data['message']);
	}

	public function testDisplayExceptionDebug()
	{
		getContainer()->get('config')->set('psx_debug', true);

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/display_error'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$data       = Json::decode((string) $body);

		$this->assertEquals(false, $data['success']);
		$this->assertEquals('foo in', substr($data['message'], 0, 6));
	}

	public function testDisplayExceptionNoDebug()
	{
		getContainer()->get('config')->set('psx_debug', false);

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/display_error'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$data       = Json::decode((string) $body);

		$this->assertEquals(false, $data['success']);
		$this->assertEquals('foo', $data['message']);
	}

	public function testException()
	{
		getContainer()->get('config')->set('psx_debug', false);

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/error'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$expect = <<<JSON
{
	"success": false,
	"message": "The server encountered an internal error and was unable to complete your request.",
	"title": "Internal Server Error"
}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
	}

	public function testExceptionHtml()
	{
		getContainer()->get('config')->set('psx_debug', false);
		//getContainer()->get('template')->set(null);

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/error'), 'GET');
		$request->addHeader('Accept', 'text/html');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$expect = <<<HTML
<!DOCTYPE>
<html>
<head>
	<title>Internal Server Error</title>
	<style type="text/css">
	body
	{
		margin:0px;
		font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;
		font-size:14px;
		line-height:1.42857143;
	}

	.title
	{
		background-color:#f2dede;
		color:#a94442;
		padding:8px;
		padding-left:32px;
	}

	.title h1
	{
		margin:0px;
	}

	.message
	{
		background-color:#333;
		color:#fff;
		padding:8px;
		padding-left:32px;
	}

	.trace
	{
		background-color:#ececec;
		padding:8px;
		padding-left:32px;
		margin-bottom:8px;
	}

	.trace pre
	{
		margin:0px;
	}

	.context
	{
		background-color:#ececec;
		padding:8px;
		padding-left:32px;
	}

	.context pre
	{
		margin:0px;
	}
	</style>
</head>

<body>

<div class="title">
	<h1>Internal Server Error</h1>
</div>

<div class="message">
	The server encountered an internal error and was unable to complete your request.</div>



</body>
</html>

HTML;

		$this->assertEquals($expect, (string) $response->getBody());
	}

	public function testExceptionXml()
	{
		getContainer()->get('config')->set('psx_debug', false);

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/error'), 'GET');
		$request->addHeader('Accept', 'application/xml');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$expect = <<<XML
<?xml version="1.0"?>
<exceptionRecord>
	<success>false</success>
	<title>Internal Server Error</title>
	<message>The server encountered an internal error and was unable to complete your request.</message>
</exceptionRecord>
XML;

		$this->assertXmlStringEqualsXmlString($expect, (string) $response->getBody());
	}

	public function testExceptionJson()
	{
		getContainer()->get('config')->set('psx_debug', false);

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/error'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$expect = <<<JSON
{
	"success": false,
	"message": "The server encountered an internal error and was unable to complete your request.",
	"title": "Internal Server Error"
}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
	}

	public function testExceptionXhr()
	{
		getContainer()->get('config')->set('psx_debug', false);

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/error'), 'GET');
		$request->addHeader('X-Requested-With', 'XMLHttpRequest');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$expect = <<<JSON
{
	"success": false,
	"message": "The server encountered an internal error and was unable to complete your request.",
	"title": "Internal Server Error"
}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/error', 'PSX\Controller\Foo\Application\TestErrorController::doError'],
			[['GET'], '/display_error', 'PSX\Controller\Foo\Application\TestErrorController::doDisplayError'],
		);
	}
}
