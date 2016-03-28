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

namespace PSX\Framework\Tests\Dispatch\Sender;

use PSX\Framework\Dispatch\Sender\Basic;
use PSX\Http\Response;
use PSX\Http\Stream\FileStream;
use PSX\Http\Stream\StringStream;

/**
 * BasicTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BasicTest extends SenderTestCase
{
    public function testSend()
    {
        $response = new Response();
        $response->setBody(new StringStream('foobar'));

        $sender = new Basic();

        $actual = $this->captureOutput($sender, $response);

        $this->assertEquals('foobar', $actual);
    }

    public function testSendHeaders()
    {
        $response = new Response();
        $response->setHeader('Content-Type', 'application/xml');
        $response->setHeader('X-Some-Header', 'foobar');
        $response->setBody(new StringStream('<foo />'));

        $sender = $this->getMockBuilder('PSX\Framework\Dispatch\Sender\Basic')
            ->setMethods(array('shouldSendHeader', 'sendHeader'))
            ->getMock();

        $sender->expects($this->once())
            ->method('shouldSendHeader')
            ->will($this->returnValue(true));

        $sender->expects($this->at(1))
            ->method('sendHeader')
            ->with($this->identicalTo('HTTP/1.1 200 OK'));

        $sender->expects($this->at(2))
            ->method('sendHeader')
            ->with($this->identicalTo('content-type: application/xml'));

        $sender->expects($this->at(3))
            ->method('sendHeader')
            ->with($this->identicalTo('x-some-header: foobar'));

        $actual = $this->captureOutput($sender, $response);

        $this->assertEquals('<foo />', $actual);
    }

    /**
     * If we have an location header we only send the location header and no
     * other content
     */
    public function testSendHeaderLocation()
    {
        $response = new Response();
        $response->setHeader('Content-Type', 'application/xml');
        $response->setHeader('Location', 'http://localhost.com');
        $response->setBody(new StringStream('<foo />'));

        $sender = $this->getMockBuilder('PSX\Framework\Dispatch\Sender\Basic')
            ->setMethods(array('shouldSendHeader', 'sendHeader'))
            ->getMock();

        $sender->expects($this->once())
            ->method('shouldSendHeader')
            ->will($this->returnValue(true));

        $sender->expects($this->at(1))
            ->method('sendHeader')
            ->with($this->identicalTo('HTTP/1.1 200 OK'));

        $sender->expects($this->at(2))
            ->method('sendHeader')
            ->with($this->identicalTo('content-type: application/xml'));

        $sender->expects($this->at(3))
            ->method('sendHeader')
            ->with($this->identicalTo('location: http://localhost.com'));

        $actual = $this->captureOutput($sender, $response);

        $this->assertEquals('', $actual);
    }

    public function testSendTransferEncodingChunked()
    {
        $response = new Response();
        $response->setHeader('Transfer-Encoding', 'chunked');
        $response->setBody(new StringStream('foobarfoobarfoobarfoobar'));

        $sender = $this->getMockBuilder('PSX\Framework\Dispatch\Sender\Basic')
            ->setMethods(array('shouldSendHeader', 'sendHeader'))
            ->getMock();

        $sender->expects($this->once())
            ->method('shouldSendHeader')
            ->will($this->returnValue(true));

        $sender->setChunkSize(16);

        $actual = $this->captureOutput($sender, $response);

        $this->assertEquals('10' . "\r\n" . 'foobarfoobarfoob' . "\r\n" . '8' . "\r\n" . 'arfoobar' . "\r\n" . '0' . "\r\n" . "\r\n", $actual);
    }

    public function testSendContentEncodingDeflate()
    {
        $response = new Response();
        $response->setHeader('Content-Encoding', 'deflate');
        $response->setBody(new StringStream('foobar'));

        $sender = $this->getMockBuilder('PSX\Framework\Dispatch\Sender\Basic')
            ->setMethods(array('shouldSendHeader', 'sendHeader'))
            ->getMock();

        $sender->expects($this->once())
            ->method('shouldSendHeader')
            ->will($this->returnValue(true));

        $actual = $this->captureOutput($sender, $response);

        $this->assertEquals(gzcompress('foobar'), $actual);
    }

    public function testSendContentEncodingGzip()
    {
        $response = new Response();
        $response->setHeader('Content-Encoding', 'gzip');
        $response->setBody(new StringStream('foobar'));

        $sender = $this->getMockBuilder('PSX\Framework\Dispatch\Sender\Basic')
            ->setMethods(array('shouldSendHeader', 'sendHeader'))
            ->getMock();

        $sender->expects($this->once())
            ->method('shouldSendHeader')
            ->will($this->returnValue(true));

        $actual = $this->captureOutput($sender, $response);

        $this->assertEquals(gzencode('foobar'), $actual);
    }

    public function testSendFileStream()
    {
        $handle = fopen('php://memory', 'r+');
        fwrite($handle, 'foobar');
        fseek($handle, 0);

        $response = new Response();
        $response->setHeader('Content-Encoding', 'gzip');
        $response->setBody(new FileStream($handle, 'foo.txt', 'text/plain'));

        $sender = $this->getMockBuilder('PSX\Framework\Dispatch\Sender\Basic')
            ->setMethods(array('shouldSendHeader', 'sendHeader'))
            ->getMock();

        $sender->expects($this->once())
            ->method('shouldSendHeader')
            ->will($this->returnValue(true));

        $actual = $this->captureOutput($sender, $response);

        $this->assertEquals('text/plain', $response->getHeader('Content-Type'));
        $this->assertEquals('attachment; filename="foo.txt"', $response->getHeader('Content-Disposition'));
        $this->assertEquals('chunked', $response->getHeader('Transfer-Encoding'));
        $this->assertEquals('6' . "\r\n" . 'foobar' . "\r\n" . '0' . "\r\n" . "\r\n", $actual);
    }

    public function testSendFileStreamNoContentType()
    {
        $handle = fopen('php://memory', 'r+');
        fwrite($handle, 'foobar');
        fseek($handle, 0);

        $response = new Response();
        $response->setHeader('Content-Encoding', 'gzip');
        $response->setBody(new FileStream($handle, 'foo.txt'));

        $sender = $this->getMockBuilder('PSX\Framework\Dispatch\Sender\Basic')
            ->setMethods(array('shouldSendHeader', 'sendHeader'))
            ->getMock();

        $sender->expects($this->once())
            ->method('shouldSendHeader')
            ->will($this->returnValue(true));

        $actual = $this->captureOutput($sender, $response);

        $this->assertEquals('application/octet-stream', $response->getHeader('Content-Type'));
        $this->assertEquals('attachment; filename="foo.txt"', $response->getHeader('Content-Disposition'));
        $this->assertEquals('chunked', $response->getHeader('Transfer-Encoding'));
        $this->assertEquals('6' . "\r\n" . 'foobar' . "\r\n" . '0' . "\r\n" . "\r\n", $actual);
    }

    public function testEmpyBodyStatusCode()
    {
        $emptyCodes = array(100, 101, 204, 304);

        foreach ($emptyCodes as $statusCode) {
            $response = new Response($statusCode);
            $response->setBody(new StringStream('foobar'));

            $sender = $this->getMockBuilder('PSX\Framework\Dispatch\Sender\Basic')
                ->setMethods(array('shouldSendHeader', 'sendHeader'))
                ->getMock();

            $sender->expects($this->once())
                ->method('shouldSendHeader')
                ->will($this->returnValue(true));

            $actual = $this->captureOutput($sender, $response);

            $this->assertEmpty($actual);
        }
    }

    public function testSendStatusCode()
    {
        $response = new Response(404);
        $response->setBody(new StringStream('foobar'));

        $sender = $this->getMockBuilder('PSX\Framework\Dispatch\Sender\Basic')
            ->setMethods(array('shouldSendHeader', 'sendHeader'))
            ->getMock();

        $sender->expects($this->once())
            ->method('shouldSendHeader')
            ->will($this->returnValue(true));

        $sender->expects($this->at(1))
            ->method('sendHeader')
            ->with($this->identicalTo('HTTP/1.1 404 Not Found'));

        $actual = $this->captureOutput($sender, $response);

        $this->assertEquals('foobar', $actual);
    }
}
