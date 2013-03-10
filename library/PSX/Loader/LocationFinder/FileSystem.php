<?php
/*
 *  $Id: Exception.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Loader\LocationFinder;

use PSX\Loader\InvalidPathException;
use PSX\Loader\Location;
use PSX\Loader\LocationFinderInterface;
use ReflectionClass;

/**
 * Basic implementation of an LocationFinder wich resolves an module depending 
 * on the filesystem. The path has the following format: /[file/path]/class. The 
 * rest of the path is provided to the loader to  determine wich method should 
 * be called
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Loader
 * @version    $Revision: 480 $
 */
class FileSystem implements LocationFinderInterface
{
	protected $path;

	/**
	 * The $path is the path where the location finder searches for modules
	 *
	 * @param string $path
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	public function resolve($pathInfo)
	{
		if(strpos($pathInfo, '..') !== false)
		{
			throw new InvalidPathException('Invalid signs in input');
		}

		$location = $this->getLocation($pathInfo);

		if($location !== false)
		{
			list($file, $path, $class) = $location;

			// include class
			require_once($file);

			// create class
			$namespace = $this->getNamespace($path);

			$class = new ReflectionClass($namespace . '\\' . $class);

			// remove path and class
			$rest = $pathInfo;

			if(!empty($path))
			{
				$rest = self::removePathPart($path, $rest);
			}

			$rest = self::removePathPart($class->getShortName(), $rest);

			// return location
			return new Location(md5($file), $rest, $class);
		}
	}

	/**
	 * The namespace is the path to the file. I.e. if our file wich is loaded is 
	 * in foo/bar/index.php the namespace of the index class must be foo\bar
	 *
	 * @param string $path
	 * @return string
	 */
	protected function getNamespace($path)
	{
		if(!empty($path))
		{
			return '\\' . str_replace('/', '\\', $path);
		}
		else
		{
			return '';
		}
	}

	protected function getLocation($path)
	{
		$path     = trim($path, '/');
		$explicit = $this->path . '/' . $path . '.php';
		$default  = $this->path . '/' . (!empty($path) ? $path . '/' : '') . 'index.php';

		if(is_file($explicit))
		{
			$file = $explicit;
			$pos  = strrpos($path, '/');

			if($pos === false)
			{
				$class = $path;
				$path  = '';
			}
			else
			{
				$class = substr($path, $pos + 1);
				$path  = substr($path, 0, $pos);
			}

			return array(
				$file,
				$path,
				$class,
			);
		}
		else if(is_file($default))
		{
			$file  = $default;
			$class = 'index';

			return array(
				$file,
				$path,
				$class,
			);
		}
		else
		{
			$pos = strrpos($path, '/');

			if($pos !== false)
			{
				return $this->getLocation(substr($path, 0, $pos));
			}
			else
			{
				return false;
			}
		}
	}

	public static function removePathPart($part, $path)
	{
		$path = trim($path, '/');
		$len  = strlen($part);

		if(substr($path, 0, $len) == $part)
		{
			return substr($path, $len + 1);
		}
		else
		{
			return $path;
		}
	}
}
