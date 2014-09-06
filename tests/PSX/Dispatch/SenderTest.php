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

namespace PSX\Dispatch;

use PSX\Http\Response;
use PSX\Http\Stream\StringStream;

/**
 * SenderTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SenderTest extends \PHPUnit_Framework_TestCase
{
	public function testSend()
	{
		$response = new Response();
		$response->setBody(new StringStream('foobar'));

		$sender = new Sender();

		$actual = $this->captureOutput($sender, $response);

		$this->assertEquals('foobar', $actual);
	}

	public function testSendHeaders()
	{
		$response = new Response('HTTP/1.1');
		$response->setHeader('Content-Type', 'application/xml');
		$response->setHeader('X-Some-Header', 'foobar');
		$response->setBody(new StringStream('<foo />'));

		$sender = $this->getMockBuilder('PSX\Dispatch\Sender')
			->setMethods(array('isCli', 'sendHeader'))
			->getMock();

		$sender->expects($this->once())
			->method('isCli')
			->will($this->returnValue(false));

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
	}

	/**
	 * If we have an location header we only send the location header and no 
	 * other content
	 */
	public function testSendHeaderLocation()
	{
		$response = new Response('HTTP/1.1');
		$response->setHeader('Content-Type', 'application/xml');
		$response->setHeader('Location', 'http://localhost.com');
		$response->setBody(new StringStream('<foo />'));

		$sender = $this->getMockBuilder('PSX\Dispatch\Sender')
			->setMethods(array('isCli', 'sendHeader'))
			->getMock();

		$sender->expects($this->once())
			->method('isCli')
			->will($this->returnValue(false));

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

		$sender = $this->getMockBuilder('PSX\Dispatch\Sender')
			->setMethods(array('isCli', 'sendHeader'))
			->getMock();

		$sender->expects($this->once())
			->method('isCli')
			->will($this->returnValue(false));

		$sender->setChunkSize(16);

		$actual = $this->captureOutput($sender, $response);

		$this->assertEquals('10' . "\r\n" . 'foobarfoobarfoob' . "\r\n" . '8' . "\r\n" . 'arfoobar' . "\r\n" . '0' . "\r\n" . "\r\n", $actual);
	}

	public function testSendContentEncodingDeflate()
	{
		$response = new Response();
		$response->setHeader('Content-Encoding', 'deflate');
		$response->setBody(new StringStream('foobar'));

		$sender = $this->getMockBuilder('PSX\Dispatch\Sender')
			->setMethods(array('isCli', 'sendHeader'))
			->getMock();

		$sender->expects($this->once())
			->method('isCli')
			->will($this->returnValue(false));

		$actual = $this->captureOutput($sender, $response);

		$this->assertEquals(gzcompress('foobar'), $actual);
	}

	public function testSendContentEncodingGzip()
	{
		$response = new Response();
		$response->setHeader('Content-Encoding', 'gzip');
		$response->setBody(new StringStream('foobar'));

		$sender = $this->getMockBuilder('PSX\Dispatch\Sender')
			->setMethods(array('isCli', 'sendHeader'))
			->getMock();

		$sender->expects($this->once())
			->method('isCli')
			->will($this->returnValue(false));

		$actual = $this->captureOutput($sender, $response);

		$this->assertEquals(gzencode('foobar'), $actual);
	}

	protected function captureOutput(Sender $sender, Response $response)
	{
		$lastError = null;

		ob_start();

		try
		{
			$sender->send($response);
		}
		catch(\Exception $e)
		{
			$lastError = $e->getMessage();
		}

		$content = ob_get_clean();

		if($lastError !== null)
		{
			$this->fail($lastError);
		}

		return $content;
	}
}
