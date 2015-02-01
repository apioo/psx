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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
		$request->setBodyParams(['body' => 'foo']);
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
		$this->assertEquals(['body' => 'foo'], $psrRequest->getBodyParams());
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
			->withBodyParams(['body' => 'foo']);

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
		$this->assertEquals(['body' => 'foo'], $request->getBodyParams());
		$this->assertEquals(['cookie' => 'foo'], $request->getCookieParams());
		$this->assertEquals(['file' => 'foo'], $request->getFileParams());
		$this->assertEquals(['query' => 'foo'], $request->getQueryParams());
		$this->assertEquals(['server' => 'foo'], $request->getServerParams());
	}
}
