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

namespace PSX;

/**
 * Represents an URI. Provides getters to retrieve parts of the URI. The class 
 * tries to parse the given string into the URI specific components:
 *
 *   foo://example.com:8042/over/there?name=ferret#nose
 *   \_/   \______________/\_________/ \_________/ \__/
 *    |           |            |            |        |
 * scheme     authority       path        query   fragment
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc3986.txt
 */
class Uri
{
	protected $scheme;
	protected $authority;
	protected $path;
	protected $query;
	protected $fragment;

	protected $user;
	protected $password;
	protected $host;
	protected $port;
	protected $parameters;

	public function __construct($uri, $authority = null, $path = null, $query = null, $fragment = null)
	{
		if(func_num_args() == 1)
		{
			$this->parse($uri);
		}
		else
		{
			$this->scheme    = $uri;
			$this->authority = $authority;
			$this->path      = $path;
			$this->query     = $query;
			$this->fragment  = $fragment;

			$this->parseAuthority($authority);
			$this->parseParameters($query);
		}
	}

	public function getScheme()
	{
		return $this->scheme;
	}

	public function getAuthority()
	{
		return $this->authority;
	}

	public function getUserInfo()
	{
		if(!empty($this->user))
		{
			return $this->user . ($this->password !== null ? ':' . $this->password : '');
		}
		else
		{
			return '';
		}
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function getPort()
	{
		return $this->port;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function getFragment()
	{
		return $this->fragment;
	}

	public function isAbsolute()
	{
		return !empty($this->scheme);
	}

	public function getParameters()
	{
		return $this->parameters;
	}

	public function withScheme($scheme)
	{
		return new static(
			$scheme,
			$this->authority,
			$this->path,
			$this->query,
			$this->fragment
		);
	}

	public function withAuthority($authority)
	{
		return new static(
			$this->scheme,
			$authority,
			$this->path,
			$this->query,
			$this->fragment
		);
	}

	public function withPath($path)
	{
		return new static(
			$this->scheme,
			$this->authority,
			$path,
			$this->query,
			$this->fragment
		);
	}

	public function withQuery($query)
	{
		return new static(
			$this->scheme,
			$this->authority,
			$this->path,
			$query,
			$this->fragment
		);
	}

	public function withFragment($fragment)
	{
		return new static(
			$this->scheme,
			$this->authority,
			$this->path,
			$this->query,
			$fragment
		);
	}

	public function withParameters(array $parameters)
	{
		return $this->withQuery(http_build_query($parameters, '', '&'));
	}

	/**
	 * Returns the string representation of the URI
	 *
	 * @see http://tools.ietf.org/html/rfc3986#section-5.3
	 * @return string
	 */
	public function toString()
	{
		$result = '';

		if(!empty($this->scheme))
		{
			$result.= $this->scheme . ':';
		}

		if(!empty($this->authority))
		{
			$result.= '//' . $this->authority;
		}

		$result.= $this->path;

		if(!empty($this->query))
		{
			$result.= '?' . $this->query;
		}

		if(!empty($this->fragment))
		{
			$result.= '#' . $this->fragment;
		}

		return $result;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Parses the given URI into the components
	 *
	 * @see http://tools.ietf.org/html/rfc3986#appendix-B
	 * @param string $uri
	 */
	protected function parse($uri)
	{
		$uri     = rawurldecode((string) $uri);
		$matches = array();

		preg_match('!' . self::getPattern() . '!', $uri, $matches);

		$scheme    = isset($matches[2]) ? $matches[2] : null;
		$authority = isset($matches[4]) ? $matches[4] : null;
		$path      = isset($matches[5]) ? $matches[5] : null;
		$query     = isset($matches[7]) ? $matches[7] : null;
		$fragment  = isset($matches[9]) ? $matches[9] : null;

		$this->scheme    = $scheme;
		$this->authority = $authority;
		$this->path      = $path;
		$this->query     = $query;
		$this->fragment  = $fragment;

		$this->parseAuthority($authority);
		$this->parseParameters($query);
	}

	protected function parseAuthority($authority)
	{
		if(empty($authority))
		{
			return;
		}

		$userInfo = strstr($authority, '@', true);
		$part     = $userInfo === false ? $authority : substr(strstr($authority, '@'), 1);

		// in case of ipv6
		if(isset($part[0]) && $part[0] == '[')
		{
			$pos = strpos($part, ']');

			if($pos !== false)
			{
				$this->host = substr($part, 0, $pos + 1);
				$this->port = substr($part, $pos + 2);
			}
			else
			{
				$this->host = $part;
			}
		}
		else
		{
			$host = strstr($part, ':', true);

			if($host === false)
			{
				$this->host = $part;
			}
			else
			{
				$this->host = $host;
				$this->port = substr(strstr($part, ':'), 1);
			}
		}

		if(!empty($userInfo))
		{
			if(strpos($userInfo, ':') !== false)
			{
				$this->user     = strstr($userInfo, ':', true);
				$this->password = substr(strstr($userInfo, ':'), 1);
			}
			else
			{
				$this->user     = $userInfo;
			}
		}
	}

	protected function parseParameters($query)
	{
		if(!empty($query))
		{
			parse_str($query, $this->parameters);
		}
		else
		{
			$this->parameters = array();
		}
	}

	/**
	 * @see https://tools.ietf.org/html/rfc3986#appendix-B
	 */
	public static function getPattern()
	{
		return '^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?';
	}
}
