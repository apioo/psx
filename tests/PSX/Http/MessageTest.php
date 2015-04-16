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

namespace PSX\Http;

use DateTime;
use PSX\Http;
use PSX\Http\Stream\StringStream;

/**
 * MessageTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$message = new Message(['foo' => 'bar'], new StringStream('foobar'));

		$this->assertEquals(['foo' => ['bar']], $message->getHeaders());
		$this->assertInstanceOf('PSX\Http\StreamInterface', $message->getBody());
		$this->assertEquals('foobar', (string) $message->getBody());

		$message = new Message(['foo' => 'bar'], 'foobar');

		$this->assertEquals(['foo' => ['bar']], $message->getHeaders());
		$this->assertInstanceOf('PSX\Http\StreamInterface', $message->getBody());
		$this->assertEquals('foobar', (string) $message->getBody());

		$message = new Message(['foo' => 'bar']);

		$this->assertEquals(['foo' => ['bar']], $message->getHeaders());
		$this->assertInstanceOf('PSX\Http\StreamInterface', $message->getBody());
		$this->assertEquals('', (string) $message->getBody());

		$message = new Message();

		$this->assertEquals([], $message->getHeaders());
		$this->assertInstanceOf('PSX\Http\StreamInterface', $message->getBody());
		$this->assertEquals('', (string) $message->getBody());
	}

	public function testGetSetHeaders()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
			'bar' => array('foo', 'bar'),
		));

		$headers = $message->getHeaders();

		$this->assertTrue(is_array($headers['foo']));
		$this->assertEquals(array('bar'), $headers['foo']);
		$this->assertTrue(is_array($headers['bar']));
		$this->assertEquals(array('foo', 'bar'), $headers['bar']);

		foreach($headers as $name => $value)
		{
			$this->assertTrue(is_array($value));
		}

		// set headers must overwrite all existing headers
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertTrue($message->hasHeader('foo'));
		$this->assertFalse($message->hasHeader('bar'));
	}

	/**
	 * @expectedException ErrorException
	 */
	public function testSetHeadersObject()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => new \stdClass(),
		));
	}

	/**
	 * @expectedException ErrorException
	 */
	public function testSetHeadersArrayObject()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => [new \stdClass()],
		));
	}

	public function testHasHeader()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertTrue($message->hasHeader('foo'));
		$this->assertFalse($message->hasHeader('bar'));
		$this->assertTrue($message->hasHeader('FOO'));
		$this->assertFalse($message->hasHeader('BAR'));
	}

	public function testGetHeader()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertEquals('bar', $message->getHeader('foo'));
		$this->assertEquals(null, $message->getHeader('bar'));
		$this->assertEquals('bar', $message->getHeader('FOO'));
		$this->assertEquals(null, $message->getHeader('BAR'));
	}

	public function testGetHeaderLines()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertEquals(array('bar'), $message->getHeaderLines('foo'));
		$this->assertEquals(array(), $message->getHeaderLines('bar'));
		$this->assertEquals(array('bar'), $message->getHeaderLines('FOO'));
		$this->assertEquals(array(), $message->getHeaderLines('BAR'));
	}

	public function testSetHeader()
	{
		$message = new Message();

		$this->assertFalse($message->hasHeader('foo'));
		$this->assertFalse($message->hasHeader('FOO'));

		$message->setHeader('foo', 'bar');

		$this->assertTrue($message->hasHeader('foo'));
		$this->assertTrue($message->hasHeader('FOO'));
	}

	public function testAddHeader()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertEquals('bar', $message->getHeader('foo'));
		$this->assertEquals(array('bar'), $message->getHeaderLines('foo'));
		$this->assertEquals('bar', $message->getHeader('FOO'));
		$this->assertEquals(array('bar'), $message->getHeaderLines('FOO'));

		// now we add the same header again which must be added to the existing 
		// header
		$message->addHeader('foo', 'foo');

		$this->assertEquals('bar, foo', $message->getHeader('foo'));
		$this->assertEquals(array('bar', 'foo'), $message->getHeaderLines('foo'));
		$this->assertEquals('bar, foo', $message->getHeader('FOO'));
		$this->assertEquals(array('bar', 'foo'), $message->getHeaderLines('FOO'));
	}

	public function testAddHeaderCaseInsensitive()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertEquals('bar', $message->getHeader('foo'));
		$this->assertEquals(array('bar'), $message->getHeaderLines('foo'));
		$this->assertEquals('bar', $message->getHeader('FOO'));
		$this->assertEquals(array('bar'), $message->getHeaderLines('FOO'));

		// now we add the same header again which must be added to the existing 
		// header
		$message->addHeader('FOO', 'foo');

		$this->assertEquals('bar, foo', $message->getHeader('foo'));
		$this->assertEquals(array('bar', 'foo'), $message->getHeaderLines('foo'));
		$this->assertEquals('bar, foo', $message->getHeader('FOO'));
		$this->assertEquals(array('bar', 'foo'), $message->getHeaderLines('FOO'));
	}

	public function testRemoveHeader()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertTrue($message->hasHeader('foo'));
		$this->assertTrue($message->hasHeader('FOO'));

		$message->removeHeader('foo');

		$this->assertFalse($message->hasHeader('foo'));
		$this->assertFalse($message->hasHeader('FOO'));
	}

	public function testRemoveCaseInsensitive()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertTrue($message->hasHeader('foo'));
		$this->assertTrue($message->hasHeader('FOO'));

		$message->removeHeader('FOO');

		$this->assertFalse($message->hasHeader('foo'));
		$this->assertFalse($message->hasHeader('FOO'));
	}

	public function testSetBody()
	{
		$message = new Message();
		$message->setBody(new StringStream('foobar'));

		$this->assertEquals('foobar', (string) $message->getBody());
	}

	public function testSetBodyString()
	{
		$message = new Message(array(), 'foobar');

		$this->assertEquals('foobar', (string) $message->getBody());
	}

	public function testSetBodyNull()
	{
		$message = new Message(array(), null);

		$this->assertEquals('', (string) $message->getBody());
	}

	public function testSetBodyResource()
	{
		$handle = fopen('php://memory', 'r+');
		fwrite($handle, 'foobar');

		$message = new Message(array(), $handle);

		$this->assertEquals('foobar', (string) $message->getBody());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidBody()
	{
		new Message(array('foo' => 'bar'), new \stdClass());
	}
}
