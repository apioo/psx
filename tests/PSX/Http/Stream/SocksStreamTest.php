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
 * SocksStreamTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SocksStreamTest extends StreamTestCase
{
	protected function getStream()
	{
		$resource = fopen('php://memory', 'r+');
		fwrite($resource, 'foobar');
		rewind($resource);

		return new SocksStream($resource, 6, false);
	}

	public function testChunkedEncoding()
	{
		$data = '4' . "\r\n";
		$data.= 'Wiki' . "\r\n";
		$data.= '5' . "\r\n";
		$data.= 'pedia' . "\r\n";
		$data.= 'e' . "\r\n";
		$data.= ' in' . "\r\n\r\n" . 'chunks.' . "\r\n";
		$data.= '0' . "\r\n";
		$data.= "\r\n";

		$resource = fopen('php://memory', 'r+');
		fwrite($resource, $data);
		rewind($resource);

		$stream  = new SocksStream($resource, 0, true);
		$content = '';

		do
		{
			$size    = $stream->getChunkSize();
			$content.= $stream->getContents($size);

			$stream->readLine();
		}
		while($size > 0);

		$this->assertEquals('Wikipedia in' . "\r\n\r\n" . 'chunks.', $content);
	}
}
