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

namespace PSX\Http;

use PSX\Http;
use PSX\Http\Stream\StringStream;

/**
 * ResponseTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
	public function testGetLine()
	{
		$response = new Response(200);

		$this->assertEquals('HTTP/1.1 200 OK', $response->getLine());
	}

	/**
	 * @expectedException PSX\Exception
	 */
	public function testGetLineUnknownStausCode()
	{
		$response = new Response(0);
		$response->getLine();
	}

	/**
	 * @expectedException PSX\Exception
	 */
	public function testGetLineUnknownStausCodeWithNoReason()
	{
		$response = new Response(800);
		$response->getLine();
	}

	public function testGetLineUnknownStausCodeWithReason()
	{
		$response = new Response();
		$response->setStatus(800, 'Foo');

		$this->assertEquals('HTTP/1.1 800 Foo', $response->getLine());
	}

	public function testToString()
	{
		$body = new StringStream();
		$body->write('foobar');

		$response = new Response(200);
		$response->setHeader('Content-Type', 'text/html; charset=UTF-8');
		$response->setBody($body);

		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'content-type: text/html; charset=UTF-8' . Http::$newLine;
		$httpResponse.= Http::$newLine;
		$httpResponse.= 'foobar';

		$this->assertEquals($httpResponse, (string) $response);
	}
}
