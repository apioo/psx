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

namespace PSX\Http\Psr;

use Phly\Http\Response as PsrResponse;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\StringStream;

/**
 * ResponseFactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testToPsr()
	{
		$response = new Response(
			200, 
			array('Content-Type' => 'text/plain'), 
			new StringStream('foobar')
		);

		$psrResponse = ResponseFactory::toPsr($response);

		$this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $psrResponse);
		$this->assertEquals(200, $psrResponse->getStatusCode());
		$this->assertEquals('text/plain', $psrResponse->getHeader('Content-Type'));
		$this->assertInstanceOf('Psr\Http\Message\StreamableInterface', $psrResponse->getBody());
		$this->assertEquals('foobar', (string) $psrResponse->getBody());
	}

	public function testFromPsr()
	{
		$psrResponse = new PsrResponse(
			new StringStream('foobar'),
			200,
			array('Content-Type' => 'text/plain')
		);

		$response = ResponseFactory::fromPsr($psrResponse);

		$this->assertInstanceOf('PSX\Http\ResponseInterface', $response);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('text/plain', $response->getHeader('Content-Type'));
		$this->assertInstanceOf('Psr\Http\Message\StreamableInterface', $response->getBody());
		$this->assertEquals('foobar', (string) $response->getBody());
	}
}
