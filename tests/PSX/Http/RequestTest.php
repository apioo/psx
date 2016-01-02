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

namespace PSX\Http;

use PSX\Http;
use PSX\Http\Stream\StringStream;
use PSX\Url;

/**
 * RequestTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRequestTarget()
    {
        $request = new Request(new Url('http://127.0.0.1'), 'GET');

        $this->assertEquals('/', $request->getRequestTarget());

        $request->setRequestTarget('*');

        $this->assertEquals('*', $request->getRequestTarget());
    }

    public function testGetUri()
    {
        $request = new Request(new Url('http://127.0.0.1'), 'GET');

        $this->assertEquals('http://127.0.0.1', $request->getUri()->toString());

        $request->setUri(new Url('http://127.0.0.1/foo'));

        $this->assertEquals('http://127.0.0.1/foo', $request->getUri()->toString());
    }

    public function testToString()
    {
        $body = new StringStream();
        $body->write('foobar');

        $request = new Request(new Url('http://127.0.0.1'), 'POST');
        $request->setHeader('Content-Type', 'text/html; charset=UTF-8');
        $request->setBody($body);

        $httpRequest = 'POST / HTTP/1.1' . Http::$newLine;
        $httpRequest.= 'content-type: text/html; charset=UTF-8' . Http::$newLine;
        $httpRequest.= Http::$newLine;
        $httpRequest.= 'foobar';

        $this->assertEquals($httpRequest, $request->toString());
        $this->assertEquals($httpRequest, (string) $request);
    }

    public function testGetSetAttributes()
    {
        $request = new Request(new Url('http://127.0.0.1'), 'POST');
        $request->setAttribute('foo', 'bar');

        $this->assertEquals('bar', $request->getAttribute('foo'));
        $this->assertEquals(null, $request->getAttribute('bar'));
        $this->assertEquals(array('foo' => 'bar'), $request->getAttributes());

        $request->setAttribute('bar', 'foo');

        $this->assertEquals('foo', $request->getAttribute('bar'));
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'foo'), $request->getAttributes());

        $request->removeAttribute('bar');
        $request->removeAttribute('fooo'); // unknown value

        $this->assertEquals(null, $request->getAttribute('bar'));
    }
}
