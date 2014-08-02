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

namespace PSX;

use InvalidArgumentException;

/**
 * Url
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Url extends Uri
{
	protected $host;
	protected $port;
	protected $user;
	protected $pass;

	public function __construct($url)
	{
		$this->parse($url);
	}

	public function getHost()
	{
		return $this->host;
	}

	public function getPort()
	{
		return $this->port;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getPass()
	{
		return $this->pass;
	}

	public function setHost($host)
	{
		$this->host = $host;
	}

	public function setPort($port)
	{
		$this->port = $port;
	}

	public function setUser($user)
	{
		$this->user = $user;
	}

	public function setPass($pass)
	{
		$this->pass = $pass;
	}

	public function getUrl()
	{
		$url = $this->scheme . '://';

		if(!empty($this->user) && !empty($this->pass))
		{
			$url.= $this->user . ':' . $this->pass . '@';
		}

		if(!empty($this->host))
		{
			$url.= $this->host;
		}
		else
		{
			throw new Exception('No host set');
		}

		if(!empty($this->port) && $this->port != 80 && $this->port != 443)
		{
			$url.= ':' . $this->port;
		}

		if(!empty($this->path))
		{
			$url.= '/' . ltrim($this->path, '/');
		}

		if(!empty($this->query))
		{
			$url.= '?' . http_build_query($this->query, '', '&');
		}

		if(!empty($this->fragment))
		{
			$url.= '#' . $this->fragment;
		}

		return $url;
	}

	public function getParams()
	{
		return $this->query;
	}

	public function addParams(array $params)
	{
		foreach($params as $k => $v)
		{
			$this->addParam($k, $v);
		}
	}

	public function getParam($key)
	{
		return isset($this->query[$key]) ? $this->query[$key] : null;
	}

	public function setParam($key, $value)
	{
		$this->query[$key] = $value;
	}

	public function addParam($key, $value, $replace = false)
	{
		if($replace === false)
		{
			if(!isset($this->query[$key]))
			{
				$this->query[$key] = $value;
			}
		}
		else
		{
			$this->query[$key] = $value;
		}
	}

	public function deleteParam($key)
	{
		if(isset($this->query[$key]))
		{
			unset($this->query[$key]);
		}
	}

	public function issetParam($key)
	{
		return isset($this->query[$key]);
	}

	public function __toString()
	{
		return $this->getUrl();
	}

	protected function parse($url)
	{
		$url = (string) $url;

		if(substr($url, 0, 2) == '//')
		{
			$url = 'http:' . $url;
		}

		// validate url format
		if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) === false)
		{
			throw new InvalidArgumentException('Invalid url syntax');
		}

		$matches = parse_url($url);

		$this->setScheme(isset($matches['scheme']) ? $matches['scheme'] : null);
		$this->setHost(isset($matches['host']) ? $matches['host'] : null);
		$this->setPort(isset($matches['port']) ? intval($matches['port']) : null);
		$this->setUser(isset($matches['user']) ? $matches['user'] : null);
		$this->setPass(isset($matches['pass']) ? $matches['pass'] : null);
		$this->setPath(isset($matches['path']) ? $matches['path'] : null);
		$this->setFragment(isset($matches['fragment']) ? $matches['fragment'] : null);

		// build authority
		$authority = '';

		if($this->user !== null)
		{
			$authority.= $this->user;

			if($this->pass !== null)
			{
				$authority.= ':' . $this->pass;
			}

			$authority.= '@';
		}

		$authority.= $this->host;

		if($this->port !== null)
		{
			$authority.= ':' . $this->port;
		}

		$this->setAuthority($authority);

		// parse params
		$query      = isset($matches['query']) ? $matches['query'] : '';
		$parameters = array();

		if(!empty($query))
		{
			parse_str($query, $parameters);
		}

		$this->setQuery($parameters);
	}

	public static function encode($encode)
	{
		return urlencode($encode);
	}

	public static function decode($decode)
	{
		return urldecode($decode);
	}
}

