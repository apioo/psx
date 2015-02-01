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

use PSX\Http;
use PSX\Http\Stream\StringStream;
use PSX\Url;

/**
 * RequestTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

	public function testGetLine()
	{
		$request = new Request(new Url('http://127.0.0.1'), 'GET');

		$this->assertEquals('GET / HTTP/1.1', $request->getLine());
	}

	/**
	 * @expectedException PSX\Exception
	 */
	public function testGetLineNoTarget()
	{
		$request = new Request(new Url('http://127.0.0.1'), 'GET');
		$request->setRequestTarget('');
		$request->getLine();
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
