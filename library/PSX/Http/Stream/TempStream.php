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

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

/**
 * The curl handler writes the http body response into an php://temp stream 
 * which means that it will use a temporary file once the amount of data stored 
 * hits a predefined limit (the default is 2 MB). Then we read the data from 
 * this stream
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TempStream implements StreamInterface
{
	protected $resource;

	public function __construct($resource)
	{
		if(!is_resource($resource))
		{
			throw new InvalidArgumentException('Must be an resource');
		}

		$this->resource = $resource;
	}

	public function close()
	{
		if($this->isAvailable())
		{
			fclose($this->resource);
		}
	}

	public function detach()
	{
		$this->close();

		$this->resource = null;
	}

	public function getSize()
	{
		$stat = fstat($this->resource);

		return isset($stat['size']) ? $stat['size'] : false;
	}

	public function tell()
	{
		return ftell($this->resource);
	}

	public function eof()
	{
		return feof($this->resource);
	}

	public function isSeekable()
	{
		return true;
	}

	public function seek($offset, $whence = SEEK_SET)
	{
		fseek($this->resource, $offset, $whence);
	}

	public function isWritable()
	{
		return true;
	}

	public function write($string)
	{
		return fwrite($this->resource, $string);
	}

	public function isReadable()
	{
		return true;
	}

	public function read($length)
	{
		return fread($this->resource, $length);
	}

	public function getContents($length = -1)
	{
		return stream_get_contents($this->resource, $length);
	}

	public function __toString()
	{
		$content = stream_get_contents($this->resource, -1, 0);

		$this->close();

		return $content;
	}

	protected function isAvailable()
	{
		return is_resource($this->resource);
	}
}
