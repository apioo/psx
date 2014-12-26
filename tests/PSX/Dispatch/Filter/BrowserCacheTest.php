<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Dispatch\Filter;

use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Url;

/**
 * BrowserCacheTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class BrowserCacheTest extends \PHPUnit_Framework_TestCase
{
	public function testExpires()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array());
		$response = new Response();

		$handle = BrowserCache::expires(new \DateTime('1986-10-09'));
		$handle->handle($request, $response, $this->getMockFilterChain($request, $response));

		$this->assertEquals('Thu, 09 Oct 1986 00:00:00 GMT', $response->getHeader('Expires'));
	}

	public function testCacheControl()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array());
		$response = new Response();

		$handle = BrowserCache::cacheControl(
			BrowserCache::TYPE_PUBLIC | BrowserCache::TYPE_PRIVATE |
			BrowserCache::NO_CACHE | BrowserCache::NO_STORE | BrowserCache::NO_TRANSFORM |
			BrowserCache::MUST_REVALIDATE | BrowserCache::PROXY_REVALIDATE,
			1024,
			2048
		);
		$handle->handle($request, $response, $this->getMockFilterChain($request, $response));

		$this->assertEquals('public, private, no-cache, no-store, no-transform, must-revalidate, proxy-revalidate, max-age=1024, s-maxage=2048', $response->getHeader('Cache-Control'));
	}

	public function testCacheControlSpecific()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array());
		$response = new Response();

		$handle = BrowserCache::cacheControl(
			BrowserCache::TYPE_PUBLIC | 
			BrowserCache::NO_CACHE | BrowserCache::NO_STORE |
			BrowserCache::MUST_REVALIDATE,
			1024
		);
		$handle->handle($request, $response, $this->getMockFilterChain($request, $response));

		$this->assertEquals('public, no-cache, no-store, must-revalidate, max-age=1024', $response->getHeader('Cache-Control'));
	}

	public function testPreventCache()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array());
		$response = new Response();

		$handle = BrowserCache::preventCache();
		$handle->handle($request, $response, $this->getMockFilterChain($request, $response));

		$this->assertEquals('Thu, 09 Oct 1986 00:00:00 GMT', $response->getHeader('Expires'));
		$this->assertEquals('no-cache, no-store, must-revalidate', $response->getHeader('Cache-Control'));
	}

	protected function getMockFilterChain($request, $response)
	{
		$filterChain = $this->getMockBuilder('PSX\Dispatch\FilterChain')
			->setConstructorArgs(array(array()))
			->setMethods(array('handle'))
			->getMock();

		$filterChain->expects($this->once())
			->method('handle')
			->with($this->equalTo($request), $this->equalTo($response));

		return $filterChain;
	}
}
