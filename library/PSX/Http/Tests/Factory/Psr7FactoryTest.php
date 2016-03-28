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

namespace PSX\Http\Tests\Factory;

use PSX\Http\Factory\Psr7Factory;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\StringStream;
use PSX\Uri\Url;

/**
 * Psr7FactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Psr7FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateRequest()
    {
        $request = new Request(
            new Url('http://localhost.com/foo?bar=foo'),
            'GET',
            ['User-Agent' => 'foo'],
            new StringStream('foobar')
        );

        $psrRequest = Psr7Factory::createRequest($request);

        $this->assertInstanceOf('Psr\Http\Message\RequestInterface', $psrRequest);
        $this->assertEquals('GET', $psrRequest->getMethod());
        $this->assertEquals(['foo'], $psrRequest->getHeader('User-Agent'));
        $this->assertEquals('foo', $psrRequest->getHeaderLine('User-Agent'));
        $this->assertInstanceOf('Psr\Http\Message\UriInterface', $psrRequest->getUri());
        $this->assertEquals('localhost.com', $psrRequest->getUri()->getHost());
        $this->assertEquals('/foo', $psrRequest->getUri()->getPath());
        $this->assertEquals('bar=foo', $psrRequest->getUri()->getQuery());
        $this->assertInstanceOf('PSX\Http\StreamInterface', $psrRequest->getBody());
        $this->assertEquals('foobar', (string) $psrRequest->getBody());
        $this->assertTrue(is_array($psrRequest->getParsedBody()));
        $this->assertTrue(is_array($psrRequest->getCookieParams()));
        $this->assertTrue(is_array($psrRequest->getUploadedFiles()));
        $this->assertTrue(is_array($psrRequest->getQueryParams()));
        $this->assertTrue(is_array($psrRequest->getServerParams()));
    }

    public function testCreateResponse()
    {
        $response = new Response(
            200,
            array('Content-Type' => 'text/plain'),
            new StringStream('foobar')
        );

        $psrResponse = Psr7Factory::createResponse($response);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $psrResponse);
        $this->assertEquals(200, $psrResponse->getStatusCode());
        $this->assertEquals(['text/plain'], $psrResponse->getHeader('Content-Type'));
        $this->assertEquals('text/plain', $psrResponse->getHeaderLine('Content-Type'));
        $this->assertInstanceOf('PSX\Http\StreamInterface', $psrResponse->getBody());
        $this->assertEquals('foobar', (string) $psrResponse->getBody());
    }
}
