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

namespace PSX\Loader\LocationFinder;

use PSX\Base;
use PSX\Loader\InvalidPathException;
use PSX\Loader\Location;
use PSX\Loader\LocationFinderInterface;
use ReflectionClass;

/**
 * Basic implementation wich reads an routing file
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RoutingFile implements LocationFinderInterface
{
	protected $file;

	public function __construct($file)
	{
		$this->file = $file;
	}

	public function resolve($pathInfo)
	{
		$method  = Base::getRequestMethod();
		$lines   = file($this->file);
		$matches = array();

		foreach($lines as $line)
		{
			$line = trim($line);

			if(!empty($line) && $line[0] != '#')
			{
				$parts   = array_values(array_filter(explode(' ', $line)));
				$allowed = isset($parts[0]) ? explode('|', $parts[0]) : array();
				$path    = isset($parts[1]) ? trim($parts[1], '/') : null;
				$class   = isset($parts[2]) ? $parts[2] : null;

				if(in_array($method, $allowed) && substr($pathInfo, 0, strlen($path)) == $path)
				{
					$matches[strlen($path)] = $class;
				}
			}
		}

		// sort matching paths
		krsort($matches, SORT_NUMERIC);

		$class = current($matches);

		if(!empty($class))
		{
			return new Location(md5($class), substr($pathInfo, strlen($path)), new ReflectionClass($class));
		}
		else
		{
			throw new InvalidPathException('Path not found', 404);
		}
	}
}
