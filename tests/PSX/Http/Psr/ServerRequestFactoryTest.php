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

use Phly\Http\ServerRequest as PsrServerRequest;
use Phly\Http\Uri as PsrUri;
use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Http\Stream\StringStream;
use PSX\Url;

/**
 * ServerRequestFactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ServerRequestFactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testToPsr()
	{
		$request = new Request(
			new Url('http://localhost.com/foo?bar=foo'), 
			'GET', 
			['User-Agent' => 'foo'], 
			new StringStream('foobar')
		);
		$request->setParsedBody(['body' => 'foo']);
		$request->setCookieParams(['cookie' => 'foo']);
		$request->setFileParams(['file' => 'foo']);
		$request->setQueryParams(['query' => 'foo']);
		$request->setServerParams(['server' => 'foo']);

		$psrRequest = ServerRequestFactory::toPsr($request);

		$this->assertInstanceOf('Psr\Http\Message\RequestInterface', $psrRequest);
		$this->assertEquals('GET', $psrRequest->getMethod());
		$this->assertEquals('foo', $psrRequest->getHeader('User-Agent'));
		$this->assertInstanceOf('Psr\Http\Message\UriInterface', $psrRequest->getUri());
		$this->assertEquals('localhost.com', $psrRequest->getUri()->getHost());
		$this->assertEquals('/foo', $psrRequest->getUri()->getPath());
		$this->assertEquals('bar=foo', $psrRequest->getUri()->getQuery());
		$this->assertInstanceOf('Psr\Http\Message\StreamableInterface', $psrRequest->getBody());
		$this->assertEquals('foobar', (string) $psrRequest->getBody());
		$this->assertEquals(['body' => 'foo'], $psrRequest->getParsedBody());
		$this->assertEquals(['cookie' => 'foo'], $psrRequest->getCookieParams());
		$this->assertEquals(['file' => 'foo'], $psrRequest->getFileParams());
		$this->assertEquals(['query' => 'foo'], $psrRequest->getQueryParams());
		$this->assertEquals(['server' => 'foo'], $psrRequest->getServerParams());
	}

	public function testFromPsr()
	{
		$psrRequest = new PsrServerRequest(
			['server' => 'foo'],
			['file' => 'foo'],
			new PsrUri('http://localhost.com/foo?bar=foo'),
			'GET',
			new StringStream('foobar'),
			['User-Agent' => 'foo']
		);

		$psrRequest = $psrRequest
			->withCookieParams(['cookie' => 'foo'])
			->withQueryParams(['query' => 'foo'])
			->withParsedBody(['body' => 'foo']);

		$request = ServerRequestFactory::fromPsr($psrRequest);

		$this->assertInstanceOf('PSX\Http\RequestInterface', $request);
		$this->assertEquals('GET', $request->getMethod());
		$this->assertEquals('foo', $request->getHeader('User-Agent'));
		$this->assertInstanceOf('PSX\Uri', $request->getUri());
		$this->assertEquals('localhost.com', $request->getUri()->getHost());
		$this->assertEquals('/foo', $request->getUri()->getPath());
		$this->assertEquals('bar=foo', $request->getUri()->getQuery());
		$this->assertInstanceOf('Psr\Http\Message\StreamableInterface', $request->getBody());
		$this->assertEquals('foobar', (string) $request->getBody());
		$this->assertEquals(['body' => 'foo'], $request->getParsedBody());
		$this->assertEquals(['cookie' => 'foo'], $request->getCookieParams());
		$this->assertEquals(['file' => 'foo'], $request->getFileParams());
		$this->assertEquals(['query' => 'foo'], $request->getQueryParams());
		$this->assertEquals(['server' => 'foo'], $request->getServerParams());
	}
}
