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

namespace PSX\Http;

use Psr\HttpMessage\RequestInterface;
use Psr\HttpMessage\StreamInterface;
use PSX\Http;
use PSX\Url;

/**
 * Request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Request extends Message implements RequestInterface
{
	protected $url;
	protected $method;
	protected $scheme;

	protected $ssl = false;
	protected $timeout;
	protected $callback;

	protected $followLocation = false;
	protected $maxRedirects   = 8;

	/**
	 * __construct
	 *
	 * @param PSX\Url $url
	 * @param string $method
	 * @param array $header
	 * @param string $body
	 * @param string $scheme
	 */
	public function __construct(Url $url, $method, array $header = array(), $body = null, $scheme = 'HTTP/1.1')
	{
		parent::__construct($header, $body, $scheme);

		$this->setUrl($url);
		$this->setMethod($method);
		$this->setProtocolVersion($scheme);

		if($url->getScheme() == 'https')
		{
			$this->setSSL(true);
		}
	}

	/**
	 * Sets the request url and automatically adds an "Host" header with the url
	 * host
	 *
	 * @param PSX\Url $url
	 * @return void
	 */
	public function setUrl($url)
	{
		$this->url = $url instanceof Url ? $url : new Url($url);

		$this->setHeader('Host', $this->url->getHost());
	}

	/**
	 * Returns the request url
	 *
	 * @return PSX\Url
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Sets the request method
	 *
	 * @param string $method
	 * @return void
	 */
	public function setMethod($method)
	{
		$this->method = $method;
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
	 * Sets the request scheme
	 *
	 * @param string $scheme
	 * @return void
	 */
	public function setProtocolVersion($scheme)
	{
		$this->scheme = $scheme;
	}

	/**
	 * Returns the http scheme
	 *
	 * @return string
	 */
	public function getProtocolVersion()
	{
		return $this->scheme;
	}

	/**
	 * Whether this request should be made through SSL
	 *
	 * @param boolean $ssl
	 * @return void
	 */
	public function setSSL($ssl)
	{
		$this->ssl = (bool) $ssl;
	}

	/**
	 * Returns whether the request is made through ssl
	 *
	 * @return boolean
	 */
	public function isSSL()
	{
		return $this->ssl;
	}

	/**
	 * Sets the timeout in seconds
	 *
	 * @param integer $timeout
	 * @return void
	 */
	public function setTimeout($timeout)
	{
		$this->timeout = (int) $timeout;
	}

	/**
	 * Returns the timeout in seconds
	 *
	 * @return integer
	 */
	public function getTimeout()
	{
		return $this->timeout;
	}

	/**
	 * Sets a custom callback wich is called before the http request is made.
	 * The first argument of the function is an resource wich depends on the
	 * handler and the second is the http request object
	 *
	 * @param Closure $callback
	 * @return void
	 */
	public function setCallback($callback)
	{
		$this->callback = $callback;
	}

	/**
	 * Returns the callback if set
	 *
	 * @return Closure
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * Returns the http request line
	 *
	 * @return string
	 */
	public function getLine()
	{
		$path = $this->getUrl()->getPath();
		$path = empty($path) ? '/' : $path;

		return $this->getMethod() . ' ' . $path . ' ' . $this->getProtocolVersion();
	}

	/**
	 * Sets whether the request should follow redirection headers
	 *
	 * @param boolean $location
	 * @param integer $maxRedirects
	 * @return void
	 */
	public function setFollowLocation($location, $maxRedirects = 8)
	{
		$this->followLocation = (bool) $location;
		$this->maxRedirects   = (int) $maxRedirects;
	}

	/**
	 * Returns whether the request follows redirection headers
	 *
	 * @return boolean
	 */
	public function getFollowLocation()
	{
		return $this->followLocation;
	}

	/**
	 * Returns how many redirects the request should follow
	 *
	 * @return integer
	 */
	public function getMaxRedirects()
	{
		return $this->maxRedirects;
	}

	/**
	 * Converts the request object to an http request string
	 *
	 * @return string
	 */
	public function toString()
	{
		$request = $this->getLine() . Http::$newLine;
		$headers = ResponseParser::buildHeaderFromMessage($this);

		foreach($headers as $header)
		{
			$request.= $header . Http::$newLine;
		}

		$request.= Http::$newLine;
		$request.= (string) $this->getBody();

		return $request;
	}

	public function __toString()
	{
		return $this->toString();
	}
}

