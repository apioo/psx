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

namespace PSX;

/**
 * Represents an URI. Provides getters and settes to modify parts of the URI.
 * The class tries to parse the given string into the URI specific components:
 *
 *   foo://example.com:8042/over/there?name=ferret#nose
 *   \_/   \______________/\_________/ \_________/ \__/
 *    |           |            |            |        |
 * scheme     authority       path        query   fragment
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc3986.txt
 */
class Uri
{
	const PATTERN_SCHEME     = 'A-z0-9\+\-\.';
	const PATTERN_UNRESERVED = 'A-z0-9\-\.\_\~';
	const PATTERN_GEN_DELIMS = '\:\/\?\#\[\]\@';
	const PATTERN_SUB_DELIMS = '\!\$\&\\\'\(\)\*\+\,\;\=';

	protected $scheme;
	protected $authority;
	protected $path;
	protected $query;
	protected $fragment;

	protected $userInfo;
	protected $host;
	protected $port;
	protected $parameters = array();

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
		if(!preg_match('/^[' . self::PATTERN_SCHEME . ']+$/', $scheme))
		{
			throw new \InvalidArgumentException('Scheme contains invalid characters');
		}

		$this->scheme = $scheme;
	}

	public function getAuthority()
	{
		return $this->authority;
	}

	public function setAuthority($authority)
	{
		$this->authority = $authority;

		list($userInfo, $host, $port) = $this->_splitAuthority($authority);

		if(!empty($userInfo))
		{
			$this->setUserInfo($userInfo);
		}

		if(!empty($host))
		{
			$this->setHost($host);
		}

		if(!empty($port))
		{
			$this->setPort($port);
		}
	}

	public function getUserInfo()
	{
		return $this->userInfo;
	}

	public function setUserInfo($userInfo)
	{
		$this->userInfo = $userInfo;

		$this->_updateAuthority();
	}

	public function getHost()
	{
		return $this->host;
	}

	public function setHost($host)
	{
		$this->host = $host;

		$this->_updateAuthority();
	}

	public function getPort()
	{
		return $this->port;
	}

	public function setPort($port)
	{
		$this->port = $port;

		$this->_updateAuthority();
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

		if(!empty($this->query))
		{
			parse_str($this->query, $this->parameters);
		}
	}

	public function getFragment()
	{
		return $this->fragment;
	}

	public function setFragment($fragment)
	{
		$this->fragment = $fragment;
	}

	public function hasParameter($name)
	{
		return isset($this->parameters[$name]);
	}

	public function getParameter($name)
	{
		return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
	}

	public function setParameter($name, $value)
	{
		$this->parameters[$name] = $value;

		$this->_updateQuery();
	}

	public function removeParameter($name)
	{
		if(array_key_exists($name, $this->parameters))
		{
			unset($this->parameters[$name]);

			$this->_updateQuery();
		}
	}

	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;

		$this->_updateQuery();
	}

	public function getParameters()
	{
		return $this->parameters;
	}

	public function isAbsolute()
	{
		return !empty($this->scheme);
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
	 * Compares this URI against another URI and returns whether they are equal
	 *
	 * @return boolean
	 */
	public function equals($uri)
	{
		if(is_string($uri))
		{
			$uri = new static($uri);
		}
		else if($uri instanceof Uri)
		{
		}
		else
		{
			return false;
		}

		return strcasecmp($this->toString(), $uri->toString()) === 0;
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
			$this->setScheme($scheme);
		}

		if(!empty($authority))
		{
			$this->setAuthority($authority);
		}
		
		if(!empty($path))
		{
			$this->setPath($path);
		}
		
		if(!empty($query))
		{
			$this->setQuery($query);
		}
		
		if(!empty($fragment))
		{
			$this->setFragment($fragment);
		}
	}

	private function _splitAuthority($authority)
	{
		if(empty($authority))
		{
			return array(null, null, null);
		}

		$userInfo = strstr($authority, '@', true);
		$part     = $userInfo === false ? $authority : substr(strstr($authority, '@'), 1);

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

	private function _updateAuthority()
	{
		$authority = '';

		if(!empty($this->userInfo))
		{
			$authority.= $this->userInfo . '@';
		}

		$authority.= $this->host;

		if(!empty($this->port))
		{
			$authority.= ':' . $this->port;
		}

		$this->authority = $authority;
	}

	private function _updateQuery()
	{
		$this->query = http_build_query($this->parameters);
	}
}
