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
 * MultipartStream
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class MultipartStream implements StreamableInterface, \Iterator, \Countable
{
	protected $streams;

	private $_pointer;

	public function __construct(array $streams)
	{
		$this->streams = $streams;
	}

	public function close()
	{
		return $this->current()->close();
	}

	public function detach()
	{
		return $this->current()->detach();
	}

	public function attach($stream)
	{
		return $this->current()->attach($stream);
	}

	public function getSize()
	{
		return $this->current()->getSize();
	}

	public function tell()
	{
		return $this->current()->tell();
	}

	public function eof()
	{
		return $this->current()->eof();
	}

	public function isSeekable()
	{
		return $this->current()->isSeekable();
	}

	public function seek($offset, $whence = SEEK_SET)
	{
		$this->current()->seek($offset, $whence);
	}

	public function isWritable()
	{
		return $this->current()->isWritable();
	}

	public function write($string)
	{
		return $this->current()->write($string);
	}

	public function isReadable()
	{
		return $this->current()->isReadable();
	}

	public function read($length)
	{
		return $this->current()->read($length);
	}

	public function getContents($length = -1)
	{
		return $this->current()->getContents($length);
	}

	public function getMetadata($key = null)
	{
		return $this->current()->getMetadata($key);
	}

	public function __toString()
	{
		return $this->current()->__toString();
	}

	public function current()
	{
		return current($this->streams);
	}

	public function key()
	{
		return key($this->streams);
	}

	public function next()
	{
		return $this->_pointer = next($this->streams);
	}

	public function rewind()
	{
		$this->_pointer = reset($this->streams);
	}

	public function valid()
	{
		return $this->_pointer;
	}

	public function count()
	{
		return count($this->streams);
	}

	public function get($key)
	{
		return isset($this->streams[$key]) ? $this->streams[$key] : null;
	}

	public static function createFromEnvironment()
	{
		$streams = array();

		foreach($_FILES as $name => $file)
		{
			if(is_uploaded_file($file['tmp_name']))
			{
				$streams[$name] = new FileStream(fopen($file['tmp_name'], 'r'), $file['name'], $file['type']);
			}
		}

		foreach($_POST as $name => $data)
		{
			$streams[$name] = new StringStream($data);
		}

		return new self($streams);
	}
}
