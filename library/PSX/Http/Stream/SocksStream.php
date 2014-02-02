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
 * In contrast to the temp stream the socks stream is actually a real stream.
 * That means only when you read data from the stream data is transfered over 
 * the wire it gets not buffered into any memory or file
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SocksStream
{
	protected $resource;
	protected $contentLength;
	protected $chunkedEncoding;

	public function __construct($resource, $contentLength, $chunkedEncoding = false)
	{
		if(!is_resource($resource))
		{
			throw new InvalidArgumentException('Must be an resource');
		}

		$this->resource        = $resource;
		$this->contentLength   = $contentLength;
		$this->chunkedEncoding = $chunkedEncoding;
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
		return $this->contentLength;
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

	public function isWriteable()
	{
		return false;
	}

	public function write($string)
	{
		return false;
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
		if($length !== -1)
		{
			$content = '';
			$read    = 0;
			$buffer  = $length;

			do
			{
				$content.= stream_get_contents($this->resource, $buffer);
				$read   += strlen($content);
				$buffer  = $buffer - $read;
			}
			while($read < $length);

			return $content;
		}
		else
		{
			return stream_get_contents($this->resource);
		}
	}

	public function isChunkEncoded()
	{
		return $this->chunkedEncoding;
	}

	public function getChunkSize()
	{
		return hexdec(trim(fgets($this->resource)));
	}

	public function readLine()
	{
		return fgets($this->resource);
	}

	public function __toString()
	{
		if($this->contentLength > 0)
		{
			$body = $this->getContents($this->contentLength);
		}
		else if($this->chunkedEncoding)
		{
			$body = '';

			do
			{
				$size = $this->getChunkSize();
				$body.= $this->getContents($size);

				$this->readLine();
			}
			while($size > 0);
		}
		else
		{
			$body = $this->getContents();
		}

		$this->close();

		return $body;
	}

	protected function isAvailable()
	{
		return is_resource($this->resource);
	}
}
