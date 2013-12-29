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
					$block->addAnnotation($key, $value);
				}
			}
		}

		return $block;
	}
}

