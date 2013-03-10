<?php
/*
 *  $Id: Url.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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
 * PSX_Url
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Url
 * @version    $Revision: 480 $
 */
class Url extends Uri
{
	protected $host;
	protected $port;
	protected $user;
	protected $pass;

	public function __construct($url)
	{
		$parts = self::parse($url);

		$this->setScheme($parts['scheme']);
		$this->setAuthority($parts['authority']);
		$this->setHost($parts['host']);
		$this->setPort($parts['port']);
		$this->setUser($parts['user']);
		$this->setPass($parts['pass']);
		$this->setPath($parts['path']);
		$this->setQuery($parts['query']);
		$this->setFragment($parts['fragment']);
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

	public function getContent()
	{
		if(in_array($this->getScheme(), stream_get_wrappers()))
		{
			return file_get_contents($this->getUrl());
		}
		else
		{
			throw new Exception('Scheme not supported');
		}
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

	public static function encode($encode)
	{
		return urlencode($encode);
	}

	public static function decode($decode)
	{
		return urldecode($decode);
	}

	public static function parse($url)
	{
		$url = (string) $url;

		// validate url format
		if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) === false)
		{
			throw new Exception('Invalid url syntax');
		}

		$matches = parse_url($url);

		$parts = array(
			'scheme'    => isset($matches['scheme'])   ? $matches['scheme']       : null,
			'authority' => null,
			'host'      => isset($matches['host'])     ? $matches['host']         : null,
			'port'      => isset($matches['port'])     ? intval($matches['port']) : null,
			'user'      => isset($matches['user'])     ? $matches['user']         : null,
			'pass'      => isset($matches['pass'])     ? $matches['pass']         : null,
			'path'      => isset($matches['path'])     ? $matches['path']         : null,
			'query'     => isset($matches['query'])    ? $matches['query']        : array(),
			'fragment'  => isset($matches['fragment']) ? $matches['fragment']     : null,
		);

		// build authority
		$authority = '';

		if($parts['user'] !== null)
		{
			$authority.= $parts['user'];

			if($parts['pass'] !== null)
			{
				$authority.= ':' . $parts['pass'];
			}

			$authority.= '@';
		}

		$authority.= $parts['host'];

		if($parts['port'] !== null)
		{
			$authority.= ':' . $parts['port'];
		}

		$parts['authority'] = $authority;

		// parse params
		if(!empty($parts['query']))
		{
			$query = array();

			parse_str($parts['query'], $query);

			$parts['query'] = $query;
		}

		return $parts;
	}
}

