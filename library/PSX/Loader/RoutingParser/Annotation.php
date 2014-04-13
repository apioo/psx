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

namespace PSX\Loader\RoutingParser;

use PSX\Loader\RoutingCollection;
use PSX\Loader\RoutingParserInterface;
use PSX\Util\Annotation as AnnotationParser;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;

/**
 * Annotation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Annotation implements RoutingParserInterface
{
	protected $paths;

	protected $_collection;

	public function __construct(array $paths)
	{
		$this->paths = $paths;
	}

	public function getCollection()
	{
		if($this->_collection === null)
		{
			$collection = new RoutingCollection();

			foreach($this->paths as $path)
			{
				$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

				foreach($files as $name => $file)
				{
					if(strpos($name, '.php') !== false)
					{
						$classes = $this->getDefinedClasses($file->getPathname());

						foreach($classes as $class)
						{
							try
							{
								$this->parseClass($collection, new ReflectionClass($class));
							}
							catch(ReflectionException $e)
							{
								// class does not exist
							}
						}
					}
				}
			}

			$this->_collection = $collection;
		}

		return $this->_collection;
	}

	/**
	 * @param PSX\Loader\RoutingCollection $collection
	 * @param ReflectionClass $class
	 */
	protected function parseClass(RoutingCollection $collection, ReflectionClass $class)
	{
		$methods = $class->getMethods();

		foreach($methods as $method)
		{
			if($method->isPublic())
			{
				$doc        = AnnotationParser::parse($method->getDocComment());
				$httpMethod = $doc->getFirstAnnotation('httpMethod');
				$path       = $doc->getFirstAnnotation('path');

				if(!empty($httpMethod) && !empty($path))
				{
					$allowed = explode('|', $httpMethod);
					$source  = $class->getName() . '::' . $method->getName();

					$collection->add($allowed, $path, $source);
				}
			}
		}
	}

	/**
	 * @param string $file
	 * @return array
	 */
	protected function getDefinedClasses($file)
	{
		$files = get_included_files();
		$file  = realpath($file);

		if(in_array($file, $files))
		{
			// if the file is already included we cant use include_once. Since
			// we assume PSR-0/4 standard we simply loop through all declared
			// classes and add the class which is in the file path
			$existingClasses = get_declared_classes();
			$newClasses      = array();

			foreach($existingClasses as $class)
			{
				if(strpos($file, str_replace('\\', DIRECTORY_SEPARATOR, $class)) !== false)
				{
					$newClasses[] = $class;
				}
			}

			return $newClasses;
		}
		else
		{
			$existingClasses = get_declared_classes();

			include_once($file);

			$newClasses = get_declared_classes();

			return array_diff($newClasses, $existingClasses);
		}
	}
}
