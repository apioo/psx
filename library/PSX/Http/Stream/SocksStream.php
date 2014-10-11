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
use Psr\Http\Message\StreamableInterface;

/**
 * The socks stream is used by the socks http handler. When you read data from 
 * the stream data is transfered over the wire it gets not buffered into any 
 * memory or file
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SocksStream extends TempStream
{
	protected $resource;
	protected $contentLength;
	protected $chunkedEncoding;

	public function __construct($resource, $contentLength, $chunkedEncoding = false)
	{
		parent::__construct($resource);

		$this->contentLength   = $contentLength;
		$this->chunkedEncoding = $chunkedEncoding;
	}

	public function detach()
	{
		$this->contentLength   = null;
		$this->chunkedEncoding = false;

		return parent::detach();
	}

	public function attach($stream)
	{
	}

	public function getSize()
	{
		return $this->contentLength;
	}

	public function isWritable()
	{
		return false;
	}

	public function getContents($length = -1)
	{
		if(!$this->resource)
		{
			return '';
		}

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

	public function getMetadata($key = null)
	{
		return null;
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
		if(!$this->resource)
		{
			return '';
		}

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
}
