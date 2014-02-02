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

namespace PSX\Http\Stream;

/**
 * StreamTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class StreamTestCase extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$this->stream = $this->getStream();
	}

	protected function tearDown()
	{
		$this->stream->close();
	}

	/**
	 * Returns the stream wich gets tested. Must contain the string foobar
	 *
	 * @return Psr\Http\StreamInterface
	 */
	abstract protected function getStream();

	public function testGetSize()
	{
		$this->assertEquals(6, $this->stream->getSize());
	}

	public function testTell()
	{
		if($this->stream->isSeekable())
		{
			$this->assertEquals(0, $this->stream->tell());
			$this->stream->seek(2);
			$this->assertEquals(2, $this->stream->tell());
		}
	}

	public function testEof()
	{
		if($this->stream->isReadable())
		{
			$content = '';

			while(!$this->stream->eof())
			{
				$content.= $this->stream->read(5);
			}

			$this->assertEquals('foobar', $content);
		}
	}

	public function testIsSeekable()
	{
		$this->stream->isSeekable();
	}

	public function testSeek()
	{
		if($this->stream->isSeekable())
		{
			$this->assertEquals(0, $this->stream->tell());
			$this->stream->seek(2);
			$this->assertEquals(2, $this->stream->tell());
			$this->stream->seek(2, SEEK_CUR);
			$this->assertEquals(4, $this->stream->tell());
			$this->stream->seek(0, SEEK_END);
			$this->assertEquals(6, $this->stream->tell());
			$this->stream->seek(0);
			$this->assertEquals(0, $this->stream->tell());
		}
	}

	public function testIsWriteable()
	{
		$this->stream->isWriteable();
	}

	public function testWrite()
	{
		if($this->stream->isWriteable())
		{
			$this->stream->seek(0, SEEK_END);
			$this->stream->write('bar');
			$this->stream->write('fooooooo');
			$this->stream->seek(12);
			$this->stream->write('bar');

			$this->assertEquals('foobarbarfoobaroo', (string) $this->stream);
		}
	}

	public function testIsReadable()
	{
		$this->stream->isReadable();
	}

	public function testRead()
	{
		if($this->stream->isReadable())
		{
			$this->assertEquals('fo', $this->stream->read(2));
		}
	}

	public function testGetContents()
	{
		if($this->stream->isReadable() && $this->stream->isSeekable())
		{
			$this->assertEquals('foobar', $this->stream->getContents());

			$this->stream->seek(2);

			$this->assertEquals('obar', $this->stream->getContents());
		}
	}

	public function testToString()
	{
		$this->assertEquals('foobar', (string) $this->stream);
	}
}
