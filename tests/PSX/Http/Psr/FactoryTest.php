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

use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Http\Response;
use PSX\Http\Stream\StringStream;
use PSX\Url;

/**
 * RequestFactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testGetPsrRequest()
	{
		$request = new Request(
			new Url('http://localhost.com/foo?bar=foo'), 
			'GET', 
			array('User-Agent' => 'foo'), 
			new StringStream('foobar')
		);

		$factory    = new Factory();
		$psrRequest = $factory->getPsrRequest($request);

		$this->assertEmpty($psrRequest);

		/*
		$this->assertInstanceOf('Psr\Http\Message\RequestInterface', $psrRequest);
		$this->assertEquals('GET', $psrRequest->getMethod());
		$this->assertEquals('foo', $psrRequest->getHeader('User-Agent'));
		$this->assertInstanceOf('Psr\Http\Message\UriInterface', $psrRequest->getUri());
		$this->assertEquals('localhost.com', $psrRequest->getUri()->getHost());
		$this->assertEquals('/foo', $psrRequest->getUri()->getPath());
		$this->assertEquals('bar=foo', $psrRequest->getUri()->getQuery());
		$this->assertInstanceOf('PSX\Http\StreamInterface', $psrRequest->getBody());
		$this->assertEquals('foobar', (string) $psrRequest->getBody());
		*/
	}

	public function testGetPsrServerRequest()
	{
		$request = new Request(
			new Url('http://localhost.com/foo?bar=foo'), 
			'GET', 
			['User-Agent' => 'foo'], 
			new StringStream('foobar')
		);

		$factory    = new Factory();
		$psrRequest = $factory->getPsrServerRequest($request);

		$this->assertEmpty($psrRequest);

		/*
		$this->assertInstanceOf('Psr\Http\Message\RequestInterface', $psrRequest);
		$this->assertEquals('GET', $psrRequest->getMethod());
		$this->assertEquals('foo', $psrRequest->getHeader('User-Agent'));
		$this->assertInstanceOf('Psr\Http\Message\UriInterface', $psrRequest->getUri());
		$this->assertEquals('localhost.com', $psrRequest->getUri()->getHost());
		$this->assertEquals('/foo', $psrRequest->getUri()->getPath());
		$this->assertEquals('bar=foo', $psrRequest->getUri()->getQuery());
		$this->assertInstanceOf('PSX\Http\StreamInterface', $psrRequest->getBody());
		$this->assertEquals('foobar', (string) $psrRequest->getBody());
		$this->assertEquals(['body' => 'foo'], $psrRequest->getParsedBody());
		$this->assertEquals(['cookie' => 'foo'], $psrRequest->getCookieParams());
		$this->assertEquals(['file' => 'foo'], $psrRequest->getFileParams());
		$this->assertEquals(['query' => 'foo'], $psrRequest->getQueryParams());
		$this->assertEquals(['server' => 'foo'], $psrRequest->getServerParams());
		*/
	}

	public function testGetPsrResponse()
	{
		$response = new Response(
			200, 
			array('Content-Type' => 'text/plain'), 
			new StringStream('foobar')
		);

		$factory     = new Factory();
		$psrResponse = $factory->getPsrResponse($response);

		$this->assertEmpty($psrResponse);

		/*
		$this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $psrResponse);
		$this->assertEquals(200, $psrResponse->getStatusCode());
		$this->assertEquals('text/plain', $psrResponse->getHeader('Content-Type'));
		$this->assertInstanceOf('PSX\Http\StreamInterface', $psrResponse->getBody());
		$this->assertEquals('foobar', (string) $psrResponse->getBody());
		*/
	}

	public function testGetNativeRequest()
	{
		$psrRequest = $this->getMock('Psr\Http\Message\RequestInterface', array(
			'getProtocolVersion',
			'withProtocolVersion',
			'getHeaders',
			'hasHeader',
			'getHeader',
			'getHeaderLines',
			'withHeader',
			'withAddedHeader',
			'withoutHeader',
			'getBody',
			'withBody',
			'getRequestTarget',
			'withRequestTarget',
			'getMethod',
			'withMethod',
			'getUri',
			'withUri'
		));

		$psrRequest->method('getHeaders')
			->willReturn(array('User-Agent' => 'foo'));

		$psrRequest->method('getMethod')
			->willReturn('GET');

		$psrRequest->method('getUri')
			->willReturn('http://localhost.com/foo?bar=foo');

		$psrRequest->method('getBody')
			->willReturn(new StringStream('foobar'));

		$factory = new Factory();
		$request = $factory->getNativeRequest($psrRequest);

		$this->assertInstanceOf('PSX\Http\RequestInterface', $request);
		$this->assertEquals('GET', $request->getMethod());
		$this->assertEquals('foo', $request->getHeader('User-Agent'));
		$this->assertInstanceOf('PSX\Uri', $request->getUri());
		$this->assertEquals('localhost.com', $request->getUri()->getHost());
		$this->assertEquals('/foo', $request->getUri()->getPath());
		$this->assertEquals('bar=foo', $request->getUri()->getQuery());
		$this->assertInstanceOf('PSX\Http\StreamInterface', $request->getBody());
		$this->assertEquals('foobar', (string) $request->getBody());
	}

	public function testGetNativeServerRequest()
	{
		$psrRequest = $this->getMock('Psr\Http\Message\ServerRequestInterface', array(
			'getProtocolVersion',
			'withProtocolVersion',
			'getHeaders',
			'hasHeader',
			'getHeader',
			'getHeaderLines',
			'withHeader',
			'withAddedHeader',
			'withoutHeader',
			'getBody',
			'withBody',
			'getRequestTarget',
			'withRequestTarget',
			'getMethod',
			'withMethod',
			'getUri',
			'withUri',
			'getServerParams',
			'getCookieParams',
			'withCookieParams',
			'getQueryParams',
			'withQueryParams',
			'getFileParams',
			'getParsedBody',
			'withParsedBody',
			'getAttributes',
			'getAttribute',
			'withAttribute',
			'withoutAttribute',
		));

		$psrRequest->method('getHeaders')
			->willReturn(array('User-Agent' => 'foo'));

		$psrRequest->method('getMethod')
			->willReturn('GET');

		$psrRequest->method('getUri')
			->willReturn('http://localhost.com/foo?bar=foo');

		$psrRequest->method('getBody')
			->willReturn(new StringStream('foobar'));

		$psrRequest->method('getParsedBody')
			->willReturn(['body' => 'foo']);

		$psrRequest->method('getCookieParams')
			->willReturn(['cookie' => 'foo']);

		$psrRequest->method('getFileParams')
			->willReturn(['file' => 'foo']);

		$psrRequest->method('getQueryParams')
			->willReturn(['query' => 'foo']);

		$psrRequest->method('getServerParams')
			->willReturn(['server' => 'foo']);

		$factory = new Factory();
		$request = $factory->getNativeServerRequest($psrRequest);

		$this->assertInstanceOf('PSX\Http\RequestInterface', $request);
		$this->assertEquals('GET', $request->getMethod());
		$this->assertEquals('foo', $request->getHeader('User-Agent'));
		$this->assertInstanceOf('PSX\Uri', $request->getUri());
		$this->assertEquals('localhost.com', $request->getUri()->getHost());
		$this->assertEquals('/foo', $request->getUri()->getPath());
		$this->assertEquals('bar=foo', $request->getUri()->getQuery());
		$this->assertEquals(['bar' => 'foo'], $request->getUri()->getParameters());
		$this->assertInstanceOf('PSX\Http\StreamInterface', $request->getBody());
		$this->assertEquals('foobar', (string) $request->getBody());
	}

	public function testGetNativeResponse()
	{
		$psrResponse = $this->getMock('Psr\Http\Message\ResponseInterface', array(
			'getProtocolVersion',
			'withProtocolVersion',
			'getHeaders',
			'hasHeader',
			'getHeader',
			'getHeaderLines',
			'withHeader',
			'withAddedHeader',
			'withoutHeader',
			'getBody',
			'withBody',
			'getStatusCode',
			'withStatus',
			'getReasonPhrase',
		));

		$psrResponse->method('getHeaders')
			->willReturn(array('Content-Type' => 'text/plain'));

		$psrResponse->method('getStatusCode')
			->willReturn(200);

		$psrResponse->method('getBody')
			->willReturn(new StringStream('foobar'));

		$factory  = new Factory();
		$response = $factory->getNativeResponse($psrResponse);

		$this->assertInstanceOf('PSX\Http\ResponseInterface', $response);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('text/plain', $response->getHeader('Content-Type'));
		$this->assertInstanceOf('PSX\Http\StreamInterface', $response->getBody());
		$this->assertEquals('foobar', (string) $response->getBody());
	}
}
