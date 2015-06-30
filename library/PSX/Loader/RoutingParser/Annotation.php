<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
				$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath($path)));

				foreach($files as $name => $file)
				{
					if(strpos($name, '.php') !== false)
					{
						$classes = $this->getDefinedClasses($file->getRealPath());

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
	 * @param \PSX\Loader\RoutingCollection $collection
	 * @param \ReflectionClass $class
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
		if($this->isIncluded($file))
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

	protected function isIncluded($file)
	{
		$files = get_included_files();

		foreach($files as $includedFile)
		{
			if(realpath($includedFile) == $file)
			{
				return true;
			}
		}

		return false;
	}
}
