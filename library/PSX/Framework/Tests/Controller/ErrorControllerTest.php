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

use PSX\Json\Parser;
use PSX\Framework\Test\Assert;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;

/**
 * ErrorControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ErrorControllerTest extends ControllerTestCase
{
    public function testExceptionDebug()
    {
        Environment::getService('config')->set('psx_debug', true);

        $response = $this->sendRequest('http://127.0.0.1/error', 'GET', ['Accept' => 'application/json']);
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body);

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(false, $data->success, $body);
        $this->assertEquals('foo in', substr($data->message, 0, 6), $body);
    }

    public function testExceptionNoDebug()
    {
        Environment::getService('config')->set('psx_debug', false);

        $response = $this->sendRequest('http://127.0.0.1/error', 'GET', ['Accept' => 'application/json']);
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body);

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(false, $data->success);
        $this->assertEquals('The server encountered an internal error and was unable to complete your request.', $data->message);
    }

    public function testDisplayExceptionDebug()
    {
        Environment::getService('config')->set('psx_debug', true);

        $response = $this->sendRequest('http://127.0.0.1/display_error', 'GET', ['Accept' => 'application/json']);
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body);

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(false, $data->success);
        $this->assertEquals('foo in', substr($data->message, 0, 6));
    }

    public function testDisplayExceptionNoDebug()
    {
        Environment::getService('config')->set('psx_debug', false);

        $response = $this->sendRequest('http://127.0.0.1/display_error', 'GET', ['Accept' => 'application/json']);
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body);

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(false, $data->success);
        $this->assertEquals('foo', $data->message);
    }

    public function testException()
    {
        Environment::getService('config')->set('psx_debug', false);

        $response = $this->sendRequest('http://127.0.0.1/error', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
	"success": false,
	"message": "The server encountered an internal error and was unable to complete your request.",
	"title": "Internal Server Error"
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testExceptionHtml()
    {
        Environment::getService('config')->set('psx_debug', false);
        //Environment::getService('template')->set(null);

        $response = $this->sendRequest('http://127.0.0.1/error', 'GET', ['Accept' => 'text/html']);
        $body     = (string) $response->getBody();

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

        $this->assertEquals(null, $response->getStatusCode(), $body);

        Assert::assertStringMatchIgnoreWhitespace($expect, $body);
    }

    public function testExceptionXml()
    {
        Environment::getService('config')->set('psx_debug', false);

        $response = $this->sendRequest('http://127.0.0.1/error', 'GET', ['Accept' => 'application/xml']);
        $body     = (string) $response->getBody();

        $expect = <<<XML
<?xml version="1.0"?>
<error>
	<success>false</success>
	<title>Internal Server Error</title>
	<message>The server encountered an internal error and was unable to complete your request.</message>
</error>
XML;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    public function testExceptionJson()
    {
        Environment::getService('config')->set('psx_debug', false);

        $response = $this->sendRequest('http://127.0.0.1/error', 'GET', ['Accept' => 'application/json']);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
	"success": false,
	"message": "The server encountered an internal error and was unable to complete your request.",
	"title": "Internal Server Error"
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testExceptionXhr()
    {
        Environment::getService('config')->set('psx_debug', false);

        $response = $this->sendRequest('http://127.0.0.1/error', 'GET', ['X-Requested-With' => 'XMLHttpRequest']);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
	"success": false,
	"message": "The server encountered an internal error and was unable to complete your request.",
	"title": "Internal Server Error"
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/error', 'PSX\Framework\Tests\Controller\Foo\Application\TestErrorController::doError'],
            [['GET'], '/display_error', 'PSX\Framework\Tests\Controller\Foo\Application\TestErrorController::doDisplayError'],
        );
    }
}
