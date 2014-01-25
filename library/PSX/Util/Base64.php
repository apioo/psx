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

namespace PSX\Util;

/**
 * Simple base64 encode and decode implementation in PHP just for fun
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/rfc4648
 */
class Base64
{
	/**
	 * Encodes the given data
	 *
	 * @param string $data
	 * @param boolean $padding
	 * @return string
	 */
	public static function encode($data, $padding = true)
	{
		$alphabet = self::getAlphabet();

		$len    = strlen($data);
		$result = '';
		$remove = $bits = 0;

		for($i = 0; $i < $len; $i++)
		{
			$x = ord($data[$i]);

			if($bits == 3)
			{
				$rest = $rest << 8;
				$x    = $x | $rest;

				$remove = 4;
			}
			elseif($bits == 15)
			{
				$rest = $rest << 8;
				$x    = $x | $rest;

				$result.= $alphabet[$x >> 6];
				$result.= $alphabet[$x & 63];

				$remove = $bits = 0;
			}
			else
			{
				$remove = 2;
			}

			if($remove > 0)
			{
				$result.= $alphabet[$x >> $remove];
				$bits   = $remove == 2 ? 3 : 15;
				$rest   = $x & $bits;
			}
		}

		if($bits == 3)
		{
			$result.= $alphabet[$rest << 4];

			if($padding)
			{
				$result.= '==';
			}
		}
		else if($bits == 15)
		{
			$result.= $alphabet[$rest << 2];

			if($padding)
			{
				$result.= '=';
			}
		}

		return $result;
	}

	/**
	 * Decodes the given data
	 *
	 * @param string $data
	 * @return string
	 */
	public static function decode($data)
	{
		$alphabet = self::getAlphabet();
		$alphabet = array_flip($alphabet);
		$remove   = 0;

		if(substr($data, -2) == '==')
		{
			$data   = substr($data, 0, -2);
			$remove = 4;
		}
		else if(substr($data, -1) == '=')
		{
			$data   = substr($data, 0, -1);
			$remove = 2;
		}

		$len    = strlen($data);
		$result = '';
		$bits   = 0;

		for($i = 0; $i < $len; $i++)
		{
			if($bits > 0)
			{
				$bits = $bits << 6;
			}

			$bits = $bits | $alphabet[$data[$i]];

			if($i != 0 && ($i + 1) % 4 == 0)
			{
				$result.= chr($bits >> 16);
				$result.= chr(($bits >> 8) & 0xFF);
				$result.= chr($bits & 0xFF);

				$bits = 0;
			}
		}

		$rest = $i % 4;

		if($rest > 0)
		{
			$bits = $bits >> $remove;

			if($rest == 2)
			{
				$result.= chr($bits & 0xFF);
			}
			else if($rest == 3)
			{
				$result.= chr(($bits >> 8) & 0xFF);
				$result.= chr($bits & 0xFF);
			}
		}

		return $result;
	}

	public static function getAlphabet()
	{
		static $alphabet;

		if(!isset($alphabet))
		{
			$alphabet = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9), array('+', '/'));
		}

		return $alphabet;
	}
}
