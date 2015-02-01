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

use Psr\Http\Message\StreamableInterface;

/**
 * Stream wich works on an string therefore the size of the stream is limited to
 * the available memory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
