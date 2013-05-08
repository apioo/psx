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

use PSX\Http;
use PSX\Url;

/**
 * Request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Request extends Message
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
		parent::__construct($header, $body);

		$this->setUrl($url);
		$this->setMethod($method);
		$this->setScheme($scheme);

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
	public function setUrl(Url $url)
	{
		$this->url = $url;

		$this->addHeader('Host', $url->getHost());
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
	 * Sets the request scheme
	 *
	 * @param string $scheme
	 * @return void
	 */
	public function setScheme($scheme)
	{
		$this->scheme = $scheme;
	}

	/**
	 * Whether this request should be made through SSL
	 *
	 * @param boolean $ssl
	 * @return void
	 */
	public function setSSL($ssl)
	{
		$this->ssl = (boolean) $ssl;
	}

	/**
	 * Sets the timeout in seconds
	 *
	 * @param integer $timeout
	 * @return void
	 */
	public function setTimeout($timeout)
	{
		$this->timeout = (integer) $timeout;
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
	 * Returns the request url
	 *
	 * @return PSX\Url
	 */
	public function getUrl()
	{
		return $this->url;
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
	 * Returns the http scheme
	 *
	 * @return string
	 */
	public function getScheme()
	{
		return $this->scheme;
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
	 * Returns the timeout in seconds
	 *
	 * @return integer
	 */
	public function getTimeout()
	{
		return $this->timeout;
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

		return $this->getMethod() . ' ' . $path . ' ' . $this->getScheme();
	}

	/**
	 * Adds an http header to the request. Overwrites existing header
	 *
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function addHeader($key, $value)
	{
		$this->header[strtolower($key)] = $value;
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
		$this->followLocation = (boolean) $location;
		$this->maxRedirects   = (integer) $maxRedirects;
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

		foreach($this->header as $k => $v)
		{
			$request.= $k . ': ' . $v . Http::$newLine;
		}

		$request.= Http::$newLine;
		$request.= $this->getBody();

		return $request;
	}

	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Merges two header arrays
	 *
	 * @param array $defaultHeader
	 * @param array $customHeader
	 * @return array
	 */
	public static function mergeHeader(array $defaultHeader, array $customHeader)
	{
		$customHeader  = array_change_key_case($customHeader);
		$defaultHeader = array_change_key_case($defaultHeader);

		return array_merge($defaultHeader, $customHeader);
	}
}

