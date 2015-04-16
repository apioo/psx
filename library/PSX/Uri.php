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
	protected $path;
	protected $query;
	protected $fragment;
	protected $user;
	protected $password;
	protected $host;
	protected $port;

	private $parameters;

	public function __construct($uri)
	{
		$this->parse($uri);
	}

	public function getScheme()
	{
		return $this->scheme;
	}

	public function setScheme($scheme)
	{
		$this->scheme = $scheme;
	}

	public function getAuthority()
	{
		$authority = '';
		$userInfo  = $this->getUserInfo();

		if(!empty($userInfo))
		{
			$authority.= $userInfo . '@';
		}

		$authority.= $this->host;

		if(!empty($this->port))
		{
			$authority.= ':' . $this->port;
		}

		return $authority;
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

	public function setUser($user)
	{
		$this->user = $user;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function setPassword($password)
	{
		$this->password = $password;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function setHost($host)
	{
		$this->host = $host;
	}

	public function getPort()
	{
		return $this->port;
	}

	public function setPort($port)
	{
		$this->port = $port;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function setPath($path)
	{
		$this->path = $path;
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function setQuery($query)
	{
		$this->query = $query;
	}

	public function getFragment()
	{
		return $this->fragment;
	}

	public function setFragment($fragment)
	{
		$this->fragment = $fragment;
	}

	public function isAbsolute()
	{
		return !empty($this->scheme);
	}

	public function getParameters()
	{
		if($this->parameters !== null)
		{
			return $this->parameters;
		}

		parse_str($this->query, $this->parameters);

		return $this->parameters;
	}

	public function setParameters(array $parameters)
	{
		$this->query      = http_build_query($parameters, '', '&');
		$this->parameters = $parameters;
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

		$authority = $this->getAuthority();
		if(!empty($authority))
		{
			$result.= '//' . $authority;
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

		preg_match_all('!^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?!', $uri, $matches);

		$scheme    = isset($matches[2][0]) ? $matches[2][0] : null;
		$authority = isset($matches[4][0]) ? $matches[4][0] : null;
		$path      = isset($matches[5][0]) ? $matches[5][0] : null;
		$query     = isset($matches[7][0]) ? $matches[7][0] : null;
		$fragment  = isset($matches[9][0]) ? $matches[9][0] : null;

		if(!empty($scheme))
		{
			$this->scheme = $scheme;
		}

		if(!empty($authority))
		{
			list($userInfo, $host, $port) = $this->splitAuthority($authority);

			if(!empty($userInfo))
			{
				if(strpos($userInfo, ':') !== false)
				{
					$this->user     = strstr($userInfo, ':', true);
					$this->password = substr(strstr($userInfo, ':'), 1);
				}
				else
				{
					$this->user = $userInfo;
				}
			}

			$this->host = $host;
			$this->port = $port;
		}
		
		if(!empty($path))
		{
			$this->path = $path;
		}
		
		if(!empty($query))
		{
			$this->query = $query;
		}
		
		if(!empty($fragment))
		{
			$this->fragment = $fragment;
		}
	}

	private function splitAuthority($authority)
	{
		$userInfo = strstr($authority, '@', true);
		$part     = $userInfo === false ? $authority : substr(strstr($authority, '@'), 1);

		// in case of ipv6
		if($part[0] == '[')
		{
			$pos = strpos($part, ']');

			if($pos !== false)
			{
				$host = substr($part, 0, $pos + 1);
				$port = substr($part, $pos + 2);
			}
			else
			{
				return array($userInfo, $part, null);
			}
		}
		else
		{
			$host = strstr($part, ':', true);

			if($host === false)
			{
				$host = $part;
				$port = null;
			}
			else
			{
				$port = substr(strstr($part, ':'), 1);
			}
		}

		return array($userInfo, $host, $port);
	}
}
