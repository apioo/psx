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

namespace PSX\Http;

use DateTime;
use PSX\Http;
use PSX\Http\Stream\StringStream;

/**
 * MessageTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$message = new Message(['foo' => 'bar'], new StringStream('foobar'));

		$this->assertEquals(['foo' => ['bar']], $message->getHeaders());
		$this->assertInstanceOf('Psr\Http\Message\StreamableInterface', $message->getBody());
		$this->assertEquals('foobar', (string) $message->getBody());

		$message = new Message(['foo' => 'bar'], 'foobar');

		$this->assertEquals(['foo' => ['bar']], $message->getHeaders());
		$this->assertInstanceOf('Psr\Http\Message\StreamableInterface', $message->getBody());
		$this->assertEquals('foobar', (string) $message->getBody());

		$message = new Message(['foo' => 'bar']);

		$this->assertEquals(['foo' => ['bar']], $message->getHeaders());
		$this->assertInstanceOf('Psr\Http\Message\StreamableInterface', $message->getBody());
		$this->assertEquals('', (string) $message->getBody());

		$message = new Message();

		$this->assertEquals([], $message->getHeaders());
		$this->assertInstanceOf('Psr\Http\Message\StreamableInterface', $message->getBody());
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
