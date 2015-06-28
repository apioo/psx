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

use PSX\Http\StreamInterface;

/**
 * Buffers the complete content of the stream into an string and works from 
 * there on with the buffered data. Fills the buffer only if someone attempts to
 * read from the stream. With this stream it is possible to read multiple times 
 * from read-only streams
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BufferedStream extends StringStream
{
	protected $source;
	protected $isFilled = false;

	public function __construct(StreamInterface $stream)
	{
		$this->data   = '';
		$this->length = $stream->getSize();
		$this->source = $stream;
	}

	public function detach()
	{
		$this->fill();

		return parent::detach();
	}

	public function isWritable()
	{
		return false;
	}

	public function getContents($maxLength = -1)
	{
		$this->fill();

		return parent::getContents($maxLength);
	}

	public function read($maxLength)
	{
		$this->fill();

		return parent::read($maxLength);
	}

	public function __toString()
	{
		$this->fill();

		return parent::__toString();
	}

	protected function fill()
	{
		if(!$this->isFilled)
		{
			$this->data     = Util::toString($this->source);
			$this->isFilled = true;
		}
	}
}
