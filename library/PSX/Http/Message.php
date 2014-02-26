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

namespace PSX\Http;

/**
 * Message
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Message
{
	protected $headers;
	protected $body;

	/**
	 * __construct
	 *
	 * @param array $header
	 * @param string $body
	 */
	public function __construct(array $header = array(), $body = null)
	{
		$this->setHeaders($header);
		$this->setBody($body);
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

	public function getHeader($name, $asArray = false)
	{
		return $this->hasHeader($name) ? $this->headers[strtolower($name)]->getValue($asArray) : null;
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
	 * @return Psr\Http\StreamInterface
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * Sets the message body
	 *
	 * @param Psr\Http\StreamInterface $body
	 * @return void
	 */
	public function setBody($body = null)
	{
		$this->body = $body;
	}
}

