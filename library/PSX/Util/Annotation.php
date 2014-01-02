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

use PSX\Util\Annotation\DocBlock;

/**
 * Util class to parse annotations
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Annotation
{
	/**
	 * Parses the annotations from the given doc block
	 *
	 * @param string $doc
	 * @return PSX\Util\Annotation\DocBlock
	 */
	public static function parse($doc)
	{
		$block = new DocBlock();
		$lines = explode("\n", $doc);

		// remove first line
		unset($lines[0]);

		foreach($lines as $line)
		{
			$line = trim($line);
			$line = substr($line, 2);

			if($line[0] == '@')
			{
				$line = substr($line, 1);
				$sp   = strpos($line, ' ');
				$bp   = strpos($line, '(');

				if($sp !== false || $bp !== false)
				{
					if($sp !== false && $bp === false)
					{
						$pos = $sp;
					}
					else if($sp === false && $bp !== false)
					{
						$pos = $bp;
					}
					else
					{
						$pos = $sp < $bp ? $sp : $bp;
					}

					$key   = substr($line, 0, $pos);
					$value = substr($line, $pos);
				}
				else
				{
					$key   = $line;
					$value = null;
				}

				$key   = trim($key);
				$value = trim($value);

				if(!empty($key))
				{
					// if key contains backslashes its a namespace use only the
					// short name
					$pos = strrpos($key, '\\');
					if($pos !== false)
					{
						$key = substr($key, $pos + 1);
					}

					$block->addAnnotation($key, $value);
				}
			}
		}

		return $block;
	}

	/**
	 * Parses the constructor values from an doctrine annotation
	 *
	 * @param string $values
	 * @return array
	 */
	public static function parseAttributes($values)
	{
		$result = array();
		$values = trim($values, " \t\n\r\0\x0B()");
		$parts  = explode(',', $values);

		foreach($parts as $part)
		{
			$kv    = explode('=', $part, 2);
			$key   = trim($kv[0]);
			$value = isset($kv[1]) ? $kv[1] : '';
			$value = trim($value, " \t\n\r\0\x0B\"");

			if(!empty($key))
			{
				$result[$key] = $value;
			}
		}

		return $result;
	}
}

