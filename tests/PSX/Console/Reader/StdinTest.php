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

namespace PSX\Console\Reader;

/**
 * StdinTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class StdinTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructorEmpty()
	{
		$reader = new Stdin();

		$this->assertInstanceOf('PSX\Console\ReaderInterface', $reader);
	}

	public function testRead()
	{
		$stream = fopen('php://memory', 'r+');
		fwrite($stream, 'foobar' . "\n" . 'foobar');
		rewind($stream);

		$reader = new Stdin($stream);

		$this->assertEquals('foobar' . "\n" . 'foobar', $reader->read());
	}

	public function testReadEOTCharacterMiddle()
	{
		$stream = fopen('php://memory', 'r+');
		fwrite($stream, 'foobar' . "\n" . 'foo' . "\x04" . 'bar');
		rewind($stream);

		$reader = new Stdin($stream);

		$this->assertEquals('foobar' . "\n" . 'foo', $reader->read());
	}

	public function testReadEOTCharacterStart()
	{
		$stream = fopen('php://memory', 'r+');
		fwrite($stream, 'foobar' . "\n" . "\x04" . 'foobar');
		rewind($stream);

		$reader = new Stdin($stream);

		$this->assertEquals('foobar' . "\n", $reader->read());
	}

	public function testReadEOTCharacterEnd()
	{
		$stream = fopen('php://memory', 'r+');
		fwrite($stream, 'foobar' . "\x04" . "\n" . 'foobar');
		rewind($stream);

		$reader = new Stdin($stream);

		$this->assertEquals('foobar', $reader->read());
	}
}
