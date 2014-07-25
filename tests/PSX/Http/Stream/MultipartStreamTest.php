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

namespace PSX\Http\Stream;

/**
 * MultipartStreamTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class MultipartStreamTest extends StreamTestCase
{
	protected function getStream()
	{
		$streams = array(
			'foo' => new StringStream('foobar'),
		);

		return new MultipartStream($streams);
	}

	public function testIterator()
	{
		$streams = array(
			'foo' => new StringStream('foobar'),
			'bar' => new StringStream('foobar'),
		);

		$stream = new MultipartStream($streams);

		$this->assertInstanceOf('Iterator', $stream);
		$this->assertInstanceOf('Countable', $stream);
		$this->assertEquals(2, count($stream));

		$this->assertEquals('foo', $stream->key());
		$this->assertEquals(6, $stream->current()->getSize());

		$stream->next();

		$this->assertEquals('bar', $stream->key());
		$this->assertEquals(6, $stream->current()->getSize());

		foreach($stream as $name => $file)
		{
		}
	}
}
