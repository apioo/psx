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
		$message = new Message(array('foo' => 'bar'), new StringStream('foobar'));

		$this->assertEquals('bar', $message->getHeader('foo'));
		$this->assertEquals(array('bar'), $message->getHeaderLines('foo'));
		$this->assertEquals('foobar', (string) $message->getBody());

		$message = new Message(array('foo' => 'bar'), 'foobar');

		$this->assertEquals('bar', $message->getHeader('foo'));
		$this->assertEquals(array('bar'), $message->getHeaderLines('foo'));
		$this->assertEquals('foobar', (string) $message->getBody());

		$message = new Message(array('foo' => 'bar'));

		$this->assertEquals('bar', $message->getHeader('foo'));
		$this->assertEquals(array('bar'), $message->getHeaderLines('foo'));
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

	public function testHasHeader()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertTrue($message->hasHeader('foo'));
		$this->assertFalse($message->hasHeader('bar'));
	}

	public function testGetHeader()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertEquals('bar', $message->getHeader('foo'));
		$this->assertEquals(null, $message->getHeader('bar'));
	}

	public function testGetHeaderLines()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertEquals(array('bar'), $message->getHeaderLines('foo'));
		$this->assertEquals(array(), $message->getHeaderLines('bar'));
	}

	public function testSetHeader()
	{
		$message = new Message();

		$this->assertFalse($message->hasHeader('foo'));

		$message->setHeader('foo', 'bar');

		$this->assertTrue($message->hasHeader('foo'));
	}

	public function testAddHeader()
	{
		$message = new Message();

		$message->addHeader('foo', 'bar');

		$this->assertEquals('bar', $message->getHeader('foo'));
		$this->assertEquals(array('bar'), $message->getHeaderLines('foo'));

		// now we add the same header again which must be added to the existing 
		// header
		$message->addHeader('foo', 'foo');

		$this->assertEquals('bar, foo', $message->getHeader('foo'));
		$this->assertEquals(array('bar', 'foo'), $message->getHeaderLines('foo'));
		$this->assertEquals('bar', $message->getHeaderLines('foo')[0]);
		$this->assertEquals('foo', $message->getHeaderLines('foo')[1]);
	}

	public function testRemoveHeader()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
		));

		$this->assertTrue($message->hasHeader('foo'));

		$message->removeHeader('foo');

		$this->assertFalse($message->hasHeader('foo'));
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
