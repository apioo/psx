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
 * Stream wich works on an string therefore the size of the stream is limited to
 * the available memory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class StringStream
{
	protected $data;

	protected $_pointer = 0;

	public function __construct($data)
	{
		$this->data = $data;
		$this->len  = strlen($data);
	}

	public function close()
	{
	}

	public function detach()
	{
	}

	public function getSize()
	{
		return $this->len;
	}

	public function tell()
	{
		return $this->_pointer;
	}

	public function eof()
	{
		return $this->_pointer >= $this->len;
	}

	public function isSeekable()
	{
		return true;
	}

	public function seek($offset, $whence = SEEK_SET)
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
			$this->_pointer = $this->len + $offset;
		}
	}

	public function isWriteable()
	{
		return false;
	}

	public function write($string)
	{
	}

	public function isReadable()
	{
		return true;
	}

	public function read($length)
	{
		$data = substr($this->data, $this->_pointer, $length);

		$this->_pointer+= $length;

		return $data;
	}

	public function getContents($length = -1)
	{
		if($length == -1)
		{
			$data = substr($this->data, $this->_pointer);

			$this->_pointer = $this->len;
		}
		else
		{
			$data = substr($this->data, $this->_pointer, $length);

			$this->_pointer+= $length;
		}

		return $data;
	}

	public function __toString()
	{
		return $this->data;
	}
}
