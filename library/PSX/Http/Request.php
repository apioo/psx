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

use PSX\Exception;
use PSX\Http;
use PSX\Uri;

/**
 * Request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Request extends Message implements RequestInterface
{
	use RequestServerTrait;

	protected $requestTarget;
	protected $method;
	protected $uri;

	/**
	 * @param PSX\Uri $uri
	 * @param string $method
	 * @param array $headers
	 * @param string $body
	 */
	public function __construct(Uri $uri, $method, array $headers = array(), $body = null)
	{
		parent::__construct($headers, $body);

		$this->uri    = $uri;
		$this->method = $method;
	}

	/**
	 * Returns the request target
	 *
	 * @return string
	 */
	public function getRequestTarget()
	{
		if($this->requestTarget !== null)
		{
			return $this->requestTarget;
		}

		$target = $this->uri->getPath();
		if(empty($target))
		{
			$target = '/';
		}

		$query = $this->uri->getQuery();
		if(!empty($query))
		{
			$target.= '?' . $query;
		}

		return $target;
	}

	/**
	 * Sets the request target
	 *
	 * @param string $requestTarget
	 */
	public function setRequestTarget($requestTarget)
	{
		$this->requestTarget = $requestTarget;
	}

	/**
	 * Returns the request method
	 *
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * Sets the request method
	 *
	 * @param string $method
	 */
	public function setMethod($method)
	{
		$this->method = $method;
	}

	/**
	 * Returns the request uri
	 *
	 * @return PSX\Uri
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * Sets the request uri
	 *
	 * @param PSX\Uri $uri
	 */
	public function setUri(Uri $uri)
	{
		$this->uri = $uri;
	}

	/**
	 * Converts the request object to an http request string
	 *
	 * @return string
	 */
	public function toString()
	{
		$request = $this->getLine() . Http::$newLine;
		$headers = RequestParser::buildHeaderFromMessage($this);

		foreach($headers as $header)
		{
			$request.= $header . Http::$newLine;
		}

		$request.= Http::$newLine;
		$request.= (string) $this->getBody();

		return $request;
	}

	/**
	 * Returns the http request line
	 *
	 * @return string
	 */
	public function getLine()
	{
		$method   = $this->getMethod();
		$target   = $this->getRequestTarget();
		$protocol = $this->getProtocolVersion();

		if(empty($target))
		{
			throw new Exception('Target not set');
		}

		$method   = !empty($method) ? $method : 'GET';
		$protocol = !empty($protocol) ? $protocol : 'HTTP/1.1';

		return $method . ' ' . $target . ' ' . $protocol;
	}

	public function __toString()
	{
		return $this->toString();
	}
}
