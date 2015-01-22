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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamableInterface;
use PSX\Http;
use PSX\Uri;
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

	protected $attributes = array();

	/**
	 * __construct
	 *
	 * @param PSX\Uri $url
	 * @param string $method
	 * @param array $header
	 * @param string $body
	 * @param string $scheme
	 */
	public function __construct(Uri $url, $method, array $header = array(), $body = null, $scheme = 'HTTP/1.1')
	{
		parent::__construct($header, $body, $scheme);

		$this->setUrl($url);
		$this->setMethod($method);
		$this->setProtocolVersion($scheme);
	}

	/**
	 * Sets the request url and automatically adds an "Host" header with the url
	 * host
	 *
	 * @param PSX\Uri $url
	 * @return void
	 */
	public function setUrl($url)
	{
		$this->url = $url instanceof Uri ? $url : new Uri($url);

		$host = $this->url->getHost();
		if(!empty($host) && !$this->hasHeader('Host'))
		{
			$this->setHeader('Host', $host);
		}
	}

	/**
	 * Returns the request url
	 *
	 * @return PSX\Uri
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
	 * Returns whether the request url uses https
	 *
	 * @return boolean
	 */
	public function isSSL()
	{
		return $this->getUrl()->getScheme() == 'https';
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function getAttribute($attribute, $default = null)
	{
		return isset($this->attributes[$attribute]) ? $this->attributes[$attribute] : $default;
	}

	public function setAttributes(array $attributes)
	{
		$this->attributes = $attributes;
	}

	public function setAttribute($attribute, $value)
	{
		$this->attributes[$attribute] = $value;
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
		$path     = $this->getUrl()->getPath();
		$path     = empty($path) ? '/' : $path;
		$query    = $this->getUrl()->getParameters();
		$fragment = $this->getUrl()->getFragment();

		$encodedChars = array('%2F', '%3A', '%40', '%7E', '%21', '%24', '%26', '%5C', '%28', '%29', '%2A', '%2B', '%2C', '%3B', '%3D');
		$allowedChars = array('/', ':', '@', '~', '!', '$', '&', '\'', '(', ')', '*', '+', ',', ';', '=');
		$path         = str_replace($encodedChars, $allowedChars, rawurlencode($path));

		if(!empty($query))
		{
			$path.= '?' . http_build_query($query, '', '&');
		}

		if(!empty($fragment))
		{
			$encodedChars = array('%2F', '%3F', '%3A', '%40', '%7E', '%21', '%24', '%26', '%5C', '%28', '%29', '%2A', '%2B', '%2C', '%3B', '%3D');
			$allowedChars = array('/', '?', ':', '@', '~', '!', '$', '&', '\'', '(', ')', '*', '+', ',', ';', '=');
			$fragment     = str_replace($encodedChars, $allowedChars, rawurlencode($fragment));

			$path.= '#' . $fragment;
		}

		return $this->getMethod() . ' ' . $path . ' ' . $this->getProtocolVersion();
	}

	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Parses an raw http request into an PSX\Http\Request object. Throws an
	 * exception if the request has not an valid format
	 *
	 * @param string $content
	 * @param PSX\Url $baseUrl
	 * @return PSX\Http\Request
	 */
	public static function parse($content, Url $baseUrl = null, $mode = ParserAbstract::MODE_STRICT)
	{
		$parser = new RequestParser($baseUrl, $mode);

		return $parser->parse($content);
	}
}
