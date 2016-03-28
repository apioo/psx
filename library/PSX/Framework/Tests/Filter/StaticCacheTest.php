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

use Doctrine\Common\Cache\ArrayCache;
use PSX\Cache;
use PSX\Framework\Filter\StaticCache;
use PSX\Framework\Filter\FilterChain;
use PSX\Framework\Filter\FilterChainInterface;
use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\StringStream;
use PSX\Uri\Url;

/**
 * StaticCacheTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
        $filters[] = function (RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain) {
            $response->getBody()->write('foobar');

            $filterChain->handle($request, $response);
        };

        $filterChain = new FilterChain($filters);
        $filterChain->handle($request, $response);

        $cache  = new Cache\Pool(new ArrayCache());
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
        $filters[] = function ($request, $response, $filterChain) {
            $response->getBody()->write('foobar');

            $filterChain->handle($request, $response);
        };

        $filterChain = new FilterChain($filters);
        $filterChain->handle($request, $response);

        $cache = new Cache\Pool(new ArrayCache());
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
