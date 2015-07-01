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

namespace PSX\Http\Factory;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

/**
 * RequestFactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class NativeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateRequest()
    {
        $psrRequest = ServerRequestFactory::fromGlobals()
            ->withUri(new Uri('http://localhost.com/foo?bar=foo'))
            ->withMethod('GET')
            ->withBody(new Stream('php://memory', 'r+'))
            ->withAddedHeader('User-Agent', 'foo');

        $psrRequest->getBody()->write('foobar');

        $request = NativeFactory::createRequest($psrRequest);

        $this->assertInstanceOf('PSX\Http\RequestInterface', $request);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('foo', $request->getHeader('User-Agent'));
        $this->assertInstanceOf('PSX\Uri', $request->getUri());
        $this->assertEquals('localhost.com', $request->getUri()->getHost());
        $this->assertEquals('/foo', $request->getUri()->getPath());
        $this->assertEquals('bar=foo', $request->getUri()->getQuery());
        $this->assertEquals(['bar' => 'foo'], $request->getUri()->getParameters());
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $request->getBody());
        $this->assertEquals('foobar', (string) $request->getBody());
    }

    public function testCreateResponse()
    {
        $psrResponse = new Response();
        $psrResponse = $psrResponse->withStatus(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody(new Stream('php://memory', 'r+'));

        $psrResponse->getBody()->write('foobar');

        $response = NativeFactory::createResponse($psrResponse);

        $this->assertInstanceOf('PSX\Http\ResponseInterface', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/plain', $response->getHeader('Content-Type'));
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $response->getBody());
        $this->assertEquals('foobar', (string) $response->getBody());
    }
}
