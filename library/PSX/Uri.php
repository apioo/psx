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

/**
 * Uri
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

	public function __construct($uri)
	{
		$this->parse($uri);
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
	 * Parses the given uri into the specificed "Syntax Components"
	 *
	 * @param string $uri
	 */
	protected function parse($uri)
	{
		$uri = (string) $uri;
		$uri = rawurldecode($uri);

		$matches = array();

		preg_match_all('!^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?!', $uri, $matches);

		$this->setScheme(isset($matches[2][0]) ? $matches[2][0] : null);
		$this->setAuthority(isset($matches[4][0]) ? $matches[4][0] : null);
		$this->setQuery(isset($matches[7][0]) ? $matches[7][0] : null);
		$this->setFragment(isset($matches[9][0]) ? $matches[9][0] : null);
		$this->setPath(isset($matches[5][0]) ? $matches[5][0] : null);
	}

	/**
	 * Generates an tag uri wich is often used in atom feeds as id
	 *
	 * @see http://www.ietf.org/rfc/rfc4151.txt
	 * @return string
	 */
	public static function buildTag($authorityName, \DateTime $date, $specific, $fragment = null, $format = 'Y-m-d')
	{
		return 'tag:' . $authorityName . ',' . $date->format($format) . ':' . $specific . ($fragment !== null ? '#' . $fragment : '');
	}

	/**
	 * @param string $relativePath
	 * @return string
	 */
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

	/**
	 * Percent encodes an value
	 *
	 * @param string $value
	 * @param boolean $preventDoubleEncode
	 * @return string
	 */
	public static function percentEncode($value, $preventDoubleEncode = true)
	{
		$len = strlen($value);
		$val = '';

		for($i = 0; $i < $len; $i++)
		{
			$j = ord($value[$i]);

			if($j <= 0xFF)
			{
				// check for double encoding
				if($preventDoubleEncode)
				{
					if($j == 0x25 && $i < $len - 2)
					{
						$hex = strtoupper(substr($value, $i + 1, 2));

						if(ctype_xdigit($hex))
						{
							$val.= '%' . $hex;

							$i+= 2;
							continue;
						}
					}
				}

				// escape characters
				if(($j >= 0x41 && $j <= 0x5A) || // alpha
					($j >= 0x61 && $j <= 0x7A) || // alpha
					($j >= 0x30 && $j <= 0x39) || // digit
					$j == 0x2D || // hyphen
					$j == 0x2E || // period
					$j == 0x5F || // underscore
					$j == 0x7E) // tilde
				{
					$val.= $value[$i];
				}
				else
				{
					$hex = dechex($j);
					$hex = $j <= 0xF ? '0' . $hex : $hex;

					$val.= '%' . strtoupper($hex);
				}
			}
			else
			{
				$val.= $value[$i];
			}
		}

		return $val;
	}
}

