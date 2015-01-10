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

namespace PSX\Util;

use InvalidArgumentException;
use PSX\Uri;

/**
 * UriResolver
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc3986.txt
 */
class UriResolver
{
	/**
	 * Resolves an base uri against an target uri
	 *
	 * @param PSX\Uri $baseUri
	 * @param PSX\Uri $targetUri
	 * @return PSX\Uri
	 */
	public static function resolve(Uri $baseUri, Uri $targetUri)
	{
		if(!$baseUri->isAbsolute())
		{
			throw new InvalidArgumentException('Base uri must be absolute');
		}

		// if the target uri is absolute
		if($targetUri->isAbsolute())
		{
			$path = $targetUri->getPath();
			if(!empty($path))
			{
				$targetUri->setPath(self::removeDotSegments($path));
			}

			return $targetUri;
		}
		else
		{
			$authority = $targetUri->getAuthority();
			$path      = $targetUri->getPath();
			$query     = $targetUri->getQuery();

			if(!empty($authority))
			{
				if(!empty($path))
				{
					$targetUri->setPath(self::removeDotSegments($path));
				}
			}
			else
			{
				if(empty($path))
				{
					if(empty($query))
					{
						$targetUri->setPath($baseUri->getPath());
						$targetUri->setQuery($baseUri->getQuery());
					}
					else
					{
						$targetUri->setPath(self::merge($baseUri->getPath(), ''));
					}
				}
				else
				{
					if(substr($path, 0, 1) == '/')
					{
						$targetUri->setPath(self::removeDotSegments($path));
					}
					else
					{
						$path = self::merge($baseUri->getPath(), $path);

						$targetUri->setPath(self::removeDotSegments($path));
					}
				}

				$targetUri->setAuthority($baseUri->getAuthority());
			}

			$targetUri->setScheme($baseUri->getScheme());

			return $targetUri;
		}
	}

	/**
	 * @param string $relativePath
	 * @return string
	 */
	public static function removeDotSegments($relativePath)
	{
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

		$resolvedPath = implode('/', $path);

		if(substr($relativePath, 0, 1) == '/')
		{
			$resolvedPath = '/' . $resolvedPath;
		}

		if(trim($resolvedPath, '/') != '' && (
			substr($relativePath, -1) == '/' || 
			substr($relativePath, -2) == '/.' || 
			substr($relativePath, -3) == '/..'))
		{
			$resolvedPath = $resolvedPath . '/';
		}

		return $resolvedPath;
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

	protected static function merge($basePath, $targetPath)
	{
		$pos = strrpos($basePath, '/');

		if($pos !== false)
		{
			return substr($basePath, 0, $pos + 1) . $targetPath;
		}
		else
		{
			return $targetPath;
		}
	}
}
