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

namespace PSX\Http\Stream;

use InvalidArgumentException;
use Psr\Http\Message\StreamableInterface;

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
class TempStream implements StreamableInterface
{
	protected $resource;
	protected $seekable;
	protected $readable;
	protected $writable;

	public function __construct($resource)
	{
		if(!is_resource($resource))
		{
			throw new InvalidArgumentException('Must be an resource');
		}

		$meta = stream_get_meta_data($resource);
		$mode = $meta['mode'] . ' ';

		$this->resource = $resource;
		$this->seekable = $meta['seekable'];
		$this->writable = $mode[0] != 'r' || $mode[1] == '+';
		$this->readable = $mode[0] == 'r' || $mode[1] == '+';
	}

	public function close()
	{
		if($this->resource)
		{
			fclose($this->resource);
		}

		$this->detach();
	}

	public function detach()
	{
		$handle = $this->resource;

		$this->resource = null;
		$this->seekable = $this->writable = $this->readable = false;

		return $handle;
	}

	public function getSize()
	{
		if($this->resource)
		{
			$stat = fstat($this->resource);

			return isset($stat['size']) ? $stat['size'] : null;
		}

		return null;
	}

	public function tell()
	{
		if($this->resource)
		{
			return ftell($this->resource);
		}

		return false;
	}

	public function eof()
	{
		if($this->resource)
		{
			return feof($this->resource);
		}

		return true;
	}

	public function rewind()
	{
		if($this->resource)
		{
			return rewind($this->resource);
		}

		return true;
	}

	public function isSeekable()
	{
		return $this->seekable;
	}

	public function seek($offset, $whence = SEEK_SET)
	{
		if($this->resource && $this->seekable)
		{
			return fseek($this->resource, $offset, $whence);
		}

		return false;
	}

	public function isWritable()
	{
		return $this->writable;
	}

	public function write($string)
	{
		if($this->resource && $this->writable)
		{
			return fwrite($this->resource, $string);
		}

		return false;
	}

	public function isReadable()
	{
		return $this->readable;
	}

	public function read($length)
	{
		if($this->resource && $this->readable)
		{
			return fread($this->resource, $length);
		}

		return false;
	}

	public function getContents($length = -1)
	{
		if($this->resource)
		{
			return stream_get_contents($this->resource, $length);
		}

		return null;
	}

	public function getMetadata($key = null)
	{
		if($this->resource)
		{
			$meta = stream_get_meta_data($this->resource);

			if($key === null)
			{
				return $meta;
			}
			else
			{
				return isset($meta[$key]) ? $meta[$key] : null;
			}
		}

		return $key === null ? array() : null;
	}

	public function __toString()
	{
		if($this->resource)
		{
			return stream_get_contents($this->resource, -1, 0);
		}

		return '';
	}
}
