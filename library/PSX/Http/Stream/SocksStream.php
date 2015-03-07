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

/**
 * The socks stream is used by the socks http handler. When you read data from 
 * the stream data is transfered over the wire it gets not buffered into any 
 * memory or file
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

		return $body;
	}
}
