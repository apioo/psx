<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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
	public function testGetSetHeaders()
	{
		$message = new Message();
		$message->setHeaders(array(
			'foo' => 'bar',
			'bar' => array('foo', 'bar'),
			'oof' => new HeaderFieldValues('foo'),
			'rab' => new HeaderFieldValues(array('bar', 'foo')),
		));

		$headers = $message->getHeaders();

		$this->assertInstanceOf('PSX\Http\HeaderFieldValues', $headers['foo']);
		$this->assertEquals('bar', (string) $headers['foo']);
		$this->assertInstanceOf('PSX\Http\HeaderFieldValues', $headers['bar']);
		$this->assertEquals('foo, bar', (string) $headers['bar']);
		$this->assertInstanceOf('PSX\Http\HeaderFieldValues', $headers['oof']);
		$this->assertEquals('foo', (string) $headers['oof']);
		$this->assertInstanceOf('PSX\Http\HeaderFieldValues', $headers['rab']);
		$this->assertEquals('bar, foo', (string) $headers['rab']);

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

		$this->assertEquals('bar', (string) $message->getHeader('foo'));
		$this->assertEquals(null, $message->getHeader('bar'));
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

		$this->assertInstanceOf('PSX\Http\HeaderFieldValues', $message->getHeader('foo'));
		$this->assertEquals('bar', (string) $message->getHeader('foo'));

		// now we add the same header again which must be added to the existing 
		// header
		$message->addHeader('foo', 'foo');

		$this->assertInstanceOf('PSX\Http\HeaderFieldValues', $message->getHeader('foo'));
		$this->assertEquals('bar, foo', (string) $message->getHeader('foo'));
		$this->assertEquals('bar', $message->getHeader('foo')[0]);
		$this->assertEquals('foo', $message->getHeader('foo')[1]);
	}

	public function testAddHeaders()
	{
		$message = new Message();

		$message->addHeaders(array(
			'foo' => 'bar'
		));

		$this->assertInstanceOf('PSX\Http\HeaderFieldValues', $message->getHeader('foo'));
		$this->assertEquals('bar', (string) $message->getHeader('foo'));

		$message->addHeaders(array(
			'foo' => 'foo'
		));

		$this->assertInstanceOf('PSX\Http\HeaderFieldValues', $message->getHeader('foo'));
		$this->assertEquals('bar, foo', (string) $message->getHeader('foo'));
		$this->assertEquals('bar', $message->getHeader('foo')[0]);
		$this->assertEquals('foo', $message->getHeader('foo')[1]);
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

	public function testSetGetBody()
	{
		$message = new Message();
		$message->setBody(new StringStream('foobar'));

		$this->assertEquals('foobar', (string) $message->getBody());
	}

	public function testConstructor()
	{
		$message = new Message(array('foo' => 'bar'), new StringStream('foobar'));

		$this->assertEquals('foobar', (string) $message->getBody());
		$this->assertInstanceOf('PSX\Http\HeaderFieldValues', $message->getHeader('foo'));
		$this->assertEquals('bar', (string) $message->getHeader('foo'));

		$message = new Message(array('foo' => 'bar'));

		$this->assertInstanceOf('PSX\Http\HeaderFieldValues', $message->getHeader('foo'));
		$this->assertEquals('bar', (string) $message->getHeader('foo'));
	}
}
