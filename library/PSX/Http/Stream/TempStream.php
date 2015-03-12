<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Http\Stream;

use InvalidArgumentException;
use Psr\Http\Message\StreamableInterface;

/**
 * Stream which operates on an normal stream
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
