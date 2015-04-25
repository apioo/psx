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

namespace PSX\Controller;

use PSX\Http\Stream\TempStream;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Json;
use PSX\Loader\LocationFinder\CallbackMethod;
use PSX\Loader\InvalidPathException;
use PSX\Test\ControllerTestCase;
use PSX\Test\Environment;
use PSX\Url;

/**
 * SupportedWriterControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SupportedWriterControllerTest extends ControllerTestCase
{
	public function testIndex()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$data       = (string) $body;

		$expect = <<<XML
<record>
  <foo>bar</foo>
</record>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $data);
	}

	public function testForward()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/forward'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$data       = (string) $body;

		$expect = <<<XML
<record>
  <bar>foo</bar>
</record>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $data);
	}

	public function testError()
	{
		Environment::getService('config')->set('psx_debug', false);

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/error'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$data       = (string) $body;

		$expect = <<<XML
<error>
  <success>false</success>
  <title>Internal Server Error</title>
  <message>The server encountered an internal error and was unable to complete your request.</message>
</error>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $data);
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/', 'PSX\Controller\Foo\Application\TestSupportedWriterController::doIndex'],
			[['GET'], '/forward', 'PSX\Controller\Foo\Application\TestSupportedWriterController::doForward'],
			[['GET'], '/error', 'PSX\Controller\Foo\Application\TestSupportedWriterController::doError'],
			[['GET'], '/inherit', 'PSX\Controller\Foo\Application\TestController::doInheritSupportedWriter'],
		);
	}
}
