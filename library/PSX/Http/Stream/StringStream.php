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

use Psr\Http\Message\StreamableInterface;

/**
 * Stream wich works on an string therefore the size of the stream is limited to
 * the available memory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StringStream implements StreamableInterface
{
	protected $data;
	protected $length;

	protected $_pointer = 0;

	public function __construct($data = '')
	{
		$this->data   = $data;
		$this->length = strlen($data);
	}

	public function close()
	{
		$this->data   = null;
		$this->length = 0;
	}

	public function detach()
	{
		if($this->data !== null)
		{
			$handle = fopen('php://memory', 'r+');
			fwrite($handle, $this->data);
			fseek($handle, 0);

			$this->close();

			return $handle;
		}

		return null;
	}

	public function getSize()
	{
		return $this->length;
	}

	public function tell()
	{
		return $this->_pointer;
	}

	public function eof()
	{
		if($this->data !== null)
		{
			return $this->_pointer >= $this->length;
		}

		return true;
	}

	public function rewind()
	{
		if($this->data !== null)
		{
			$this->_pointer = 0;
		}

		return true;
	}

	public function isSeekable()
	{
		return $this->data !== null;
	}

	public function seek($offset, $whence = SEEK_SET)
	{
		if($this->isSeekable())
		{
			if($whence === SEEK_SET)
			{
				$this->_pointer = $offset;
			}
			else if($whence === SEEK_CUR)
			{
				$this->_pointer+= $offset;
			}
			else if($whence === SEEK_END)
			{
				$this->_pointer = $this->length + $offset;
			}
		}

		return false;
	}

	public function isWritable()
	{
		return $this->data !== null;
	}

	public function write($string)
	{
		if($this->isWritable())
		{
			$length  = strlen($string);
			$pre  = substr($this->data, 0, $this->_pointer);
			$post = substr($this->data, $this->_pointer + $length);

			$this->data = $pre . $string . $post;

			$this->_pointer+= $length;

			return $length;
		}

		return false;
	}

	public function isReadable()
	{
		return $this->data !== null;
	}

	public function read($maxLength)
	{
		if($this->isReadable())
		{
			$data = substr($this->data, $this->_pointer, $maxLength);

			$this->_pointer+= $maxLength;

			return $data;
		}

		return false;
	}

	public function getContents($maxLength = -1)
	{
		if($this->data === null)
		{
			return null;
		}

		if($maxLength == -1)
		{
			$data = substr($this->data, $this->_pointer);

			$this->_pointer = $this->length;
		}
		else
		{
			$data = substr($this->data, $this->_pointer, $maxLength);

			$this->_pointer+= $maxLength;
		}

		return $data;
	}

	public function getMetadata($key = null)
	{
		return $key === null ? array() : null;
	}

	public function __toString()
	{
		return $this->data === null ? '' : $this->data;
	}
}
