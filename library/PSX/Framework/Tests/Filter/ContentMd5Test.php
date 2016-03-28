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

use PSX\Framework\Filter\ContentMd5;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Uri\Url;

/**
 * ContentMd5Test
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ContentMd5Test extends \PHPUnit_Framework_TestCase
{
    public function testAddHeader()
    {
        $body = new TempStream(fopen('php://memory', 'r+'));
        $body->write('foobar');

        $request  = new Request(new Url('http://localhost'), 'GET');
        $response = new Response();
        $response->setBody($body);

        $filter = new ContentMd5();
        $filter->handle($request, $response, $this->getMockFilterChain($request, $response));

        $this->assertEquals(md5('foobar'), $response->getHeader('Content-MD5'));
        $this->assertEquals('foobar', (string) $response->getBody());
    }

    public function testHeaderExist()
    {
        $body = new TempStream(fopen('php://memory', 'r+'));
        $body->write('foobar');

        $request  = new Request(new Url('http://localhost'), 'GET');
        $response = new Response();
        $response->setHeader('Content-MD5', 'foobar');
        $response->setBody($body);

        $filter = new ContentMd5();
        $filter->handle($request, $response, $this->getMockFilterChain($request, $response));

        $this->assertEquals('foobar', $response->getHeader('Content-MD5'));
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
