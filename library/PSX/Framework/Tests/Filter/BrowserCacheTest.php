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

namespace PSX\Framework\Tests\Filter;

use PSX\Framework\Filter\BrowserCache;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Uri\Url;

/**
 * BrowserCacheTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
        $filterChain = $this->getMockBuilder('PSX\Framework\Filter\FilterChain')
            ->setConstructorArgs(array(array()))
            ->setMethods(array('handle'))
            ->getMock();

        $filterChain->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($request), $this->equalTo($response));

        return $filterChain;
    }
}
