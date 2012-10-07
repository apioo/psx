<?php
/*
 *  $Id: Uri.php 486 2012-05-28 12:42:54Z k42b3.x@googlemail.com $
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

/**
 * PSX_Uri
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @see        http://www.ietf.org/rfc/rfc3986.txt
 * @category   PSX
 * @package    PSX_Uri
 * @version    $Revision: 486 $
 */
class PSX_Uri
{
	protected $scheme;
	protected $authority;
	protected $path;
	protected $query;
	protected $fragment;

	public function __construct($uri)
	{
		$parts = self::parse($uri);

		$this->setScheme($parts['scheme']);
		$this->setAuthority($parts['authority']);
		$this->setPath($parts['path']);
		$this->setQuery($parts['query']);
		$this->setFragment($parts['fragment']);
	}

	public function getScheme()
	{
		return $this->scheme;
	}

	public function getAuthority()
	{
		return $this->authority;
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

	public function setScheme($scheme)
	{
		$this->scheme = $scheme;
	}

	public function setAuthority($authority)
	{
		$this->authority = $authority;
	}

	public function setPath($path)
	{
		$this->path = $path;
	}

	public function setQuery($query)
	{
		$this->query = $query;
	}

	public function setFragment($fragment)
	{
		$this->fragment = $fragment;
	}

	public function getUri()
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

	public function __toString()
	{
		return $this->getUri();
	}

	/**
	 * Generates an tag uri wich is often used in atom feeds as id
	 *
	 * @see http://www.ietf.org/rfc/rfc4151.txt
	 * @return string
	 */
	public static function buildTag($authorityName, DateTime $date, $specific, $fragment = null, $format = 'Y-m-d')
	{
		return 'tag:' . $authorityName . ',' . $date->format($format) . ':' . $specific . ($fragment !== null ? '#' . $fragment : '');
	}

	/**
	 * Parses the given uri into the specificed "Syntax Components". Returns an
	 * associatve array containing the defined parts. Throws an exception if its
	 * an invalid uri
	 *
	 * @return array
	 */
	public static function parse($uri)
	{
		$uri = (string) $uri;
		$uri = rawurldecode($uri);

		$matches = array();

		preg_match_all('!^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?!', $uri, $matches);

		$parts = array(
			'scheme'    => isset($matches[2][0]) ? $matches[2][0] : null,
			'authority' => isset($matches[4][0]) ? $matches[4][0] : null,
			'path'      => isset($matches[5][0]) ? $matches[5][0] : null,
			'query'     => isset($matches[7][0]) ? $matches[7][0] : null,
			'fragment'  => isset($matches[9][0]) ? $matches[9][0] : null,
		);

		if($parts['path'] !== null)
		{
			$parts['path'] = self::removeDotSegments($parts['path']);
		}

		return $parts;
	}

	public static function removeDotSegments($relativePath)
	{
		if(strpos($relativePath, '/') === false)
		{
			return $relativePath;
		}

		$parts = explode('/', $relativePath);
		$path  = array();

		foreach($parts as $part)
		{
			$part = trim($part);

			if(empty($part) || $part == '.')
			{
			}
			else if($part == '..')
			{
				array_pop($path);
			}
			else
			{
				$path[] = $part;
			}
		}

		if(!empty($path))
		{
			$absolutePath = '/' . implode('/', $path);
		}
		else
		{
			$absolutePath = '/';
		}

		return $absolutePath;
	}
}

