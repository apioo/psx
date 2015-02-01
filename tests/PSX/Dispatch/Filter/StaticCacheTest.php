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

namespace PSX\Dispatch\Filter;

use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\StringStream;
use PSX\Url;
use PSX\Cache;
use PSX\Cache\Handler as CacheHandler;
use PSX\Dispatch\FilterChain;

/**
 * StaticCacheTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class StaticCacheTest extends \PHPUnit_Framework_TestCase
{
	public function testCache()
	{
		$request  = new Request(new Url('http://localhost.com/foo/bar'), 'GET');
		$response = new Response(200, array('X-Some' => 'Stuff', 'Content-Type' => 'text/plain'));
		$response->setBody(new StringStream());

		$filters = array();
		$filters[] = function($request, $response, $filterChain){
			$response->getBody()->write('foobar');

			$filterChain->handle($request, $response);
		};

		$filterChain = new FilterChain($filters);
		$filterChain->handle($request, $response);

		$cache  = new Cache(new CacheHandler\Memory());
		$filter = new StaticCache($cache);
		$filter->handle($request, $response, $filterChain);

		$result = $cache->getItem(md5('/foo/bar'))->get();

		$this->assertArrayHasKey('headers', $result);
		$this->assertArrayHasKey('Content-Type', $result['headers']);
		$this->assertEquals('text/plain', $result['headers']['Content-Type']);
		$this->assertArrayHasKey('Last-Modified', $result['headers']);
		$this->assertArrayHasKey('body', $result);
		$this->assertEquals('foobar', $result['body']);
	}

	public function testCacheHit()
	{
		$request  = new Request(new Url('http://localhost.com/foo/bar'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$filters = array();
		$filters[] = function($request, $response, $filterChain){
			$response->getBody()->write('foobar');

			$filterChain->handle($request, $response);
		};

		$filterChain = new FilterChain($filters);
		$filterChain->handle($request, $response);

		$cache = new Cache(new CacheHandler\Memory());
		$item  = $cache->getItem(md5('/foo/bar'));
		$item->set(array(
			'headers' => array(
				'Last-Modified' => 'Sat, 27 Dec 2014 15:54:49 GMT',
				'Content-Type'  => 'text/plain',
			),
			'body' => 'foobar',
		));

		$cache->save($item);

		$filter = new StaticCache($cache);
		$filter->handle($request, $response, $filterChain);

		$result = $cache->getItem(md5('/foo/bar'))->get();

		$this->assertArrayHasKey('headers', $result);
		$this->assertArrayHasKey('Content-Type', $result['headers']);
		$this->assertEquals('text/plain', $result['headers']['Content-Type']);
		$this->assertArrayHasKey('Last-Modified', $result['headers']);
		$this->assertEquals('Sat, 27 Dec 2014 15:54:49 GMT', $result['headers']['Last-Modified']);
		$this->assertArrayHasKey('body', $result);
		$this->assertEquals('foobar', $result['body']);
	}
}
