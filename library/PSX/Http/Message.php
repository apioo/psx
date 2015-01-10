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

namespace PSX\Http;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamableInterface;
use PSX\Http\Stream\StringStream;

/**
 * Message
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Message implements MessageInterface
{
	protected $headers;
	protected $body;
	protected $scheme;

	/**
	 * @param array $header
	 * @param Psr\Http\Message\StreamableInterface|string $body
	 * @param string $scheme
	 */
	public function __construct(array $header = array(), $body = null, $scheme = null)
	{
		$this->setHeaders($header);
		$this->setBody($this->prepareBody($body));

		$this->scheme = $scheme;
	}

	public function getProtocolVersion()
	{
		return $this->scheme;
	}

	public function getHeaders()
	{
		$result = array();

		foreach($this->headers as $name => $value)
		{
			$result[$name] = $value->getValue(true);
		}

		return $result;
	}

	public function hasHeader($name)
	{
		return isset($this->headers[strtolower($name)]);
	}

	public function getHeader($name)
	{
		return $this->hasHeader($name) ? $this->headers[strtolower($name)]->getValue(false) : null;
	}

	public function getHeaderAsArray($name)
	{
		return $this->hasHeader($name) ? $this->headers[strtolower($name)]->getValue(true) : array();
	}

	public function setHeader($name, $value)
	{
		if(!$value instanceof HeaderFieldValues)
		{
			$value = new HeaderFieldValues($value);
		}

		$this->headers[strtolower($name)] = $value;
	}

	public function setHeaders(array $headers)
	{
		$this->headers = array();
		$this->addHeaders($headers);
	}

	public function addHeader($name, $value)
	{
		if($this->hasHeader($name))
		{
			$this->headers[strtolower($name)]->append($value);
		}
		else
		{
			$this->setHeader($name, $value);
		}
	}

	public function addHeaders(array $headers)
	{
		foreach($headers as $name => $value)
		{
			$this->addHeader($name, $value);
		}
	}

	public function removeHeader($name)
	{
		if(isset($this->headers[$name]))
		{
			unset($this->headers[$name]);
		}
	}

	/**
	 * Returns the message body
	 *
	 * @return Psr\Http\StreamableInterface
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * Sets the message body
	 *
	 * @param Psr\Http\StreamableInterface $body
	 * @return void
	 */
	public function setBody(StreamableInterface $body = null)
	{
		$this->body = $body;
	}

	protected function prepareBody($body)
	{
		if($body === null || $body instanceof StreamableInterface)
		{
			return $body;
		}
		else if(is_string($body))
		{
			return new StringStream($body);
		}
		else
		{
			throw new InvalidArgumentException('Body must be either an Psr\Http\Message\StreamableInterface or string');
		}
	}
}

