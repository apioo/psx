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

namespace PSX\Loader\CallbackResolver;

use UnexpectedValueException;
use PSX\ControllerInterface;
use PSX\Dispatch\RequestFilterInterface;
use PSX\Dispatch\ResponseFilterInterface;
use PSX\Exception;
use PSX\Http\Request;
use PSX\Http\response;
use PSX\Loader\Location;
use PSX\Loader\CallbackResolverInterface;
use PSX\Util\Annotation as AnnotationParser;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Annotation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Annotation implements CallbackResolverInterface
{
	protected $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function resolve(Location $location, Request $request, Response $response)
	{
		$source = $location->getSource();

		if(strpos($source, '::') === false)
		{
			$uriFragments = array();
			$class        = new ReflectionClass($location->getSource());
			$method       = $this->getMethodToCall($class, $request->getMethod(), $location->getPath(), $uriFragments);
			$controller   = $class->newInstance($this->container, $location, $request, $response, $uriFragments);

			if($method instanceof ReflectionMethod)
			{
				return array($controller, $method->getName());
			}
			else
			{
				return array($controller, null);
			}
		}
		else
		{
			list($className, $method) = explode('::', $source, 2);

			if(class_exists($className))
			{
				$class = new $className($this->container, $location, $request, $response, array());

				return array($class, $method);
			}
			else
			{
				throw new UnexpectedValueException('Class "' . $className . '" does not exists');
			}
		}
	}

	/**
	 * If a method in the controller has an docblock containing the @httpMethod 
	 * and @path annotation the loader will call the method depending on the 
	 * path and the request method. I.e. if the path is /foo/1 this will match 
	 * if the path parameter is /foo/{bar}. You can access the values in the 
	 * curly brackets with $this->getUriFragments('bar')
	 *
	 * @param ReflectionClass $class
	 * @param string $requestMethod
	 * @param string $requestPath
	 * @param array $uriFragments
	 * @return ReflectionMethod
	 */
	protected function getMethodToCall(ReflectionClass $class, $requestMethod, $requestPath, &$uriFragments)
	{
		// search method wich sould be called
		$method     = false;
		$rootMethod = false;

		$realPath = trim($requestPath, '/');
		$reserved = array('__construct', 'getStage', 'getRequestFilter', 'getResponseFilter', '__call', 'onLoad', 'onGet', 'onPost', 'onPut', 'onDelete', 'processResponse');
		$methods  = $class->getMethods();

		if(!empty($realPath))
		{
			$realPath = explode('/', $realPath);
		}
		else
		{
			$realPath = array();
		}

		foreach($methods as $m)
		{
			if($m->isPublic() && !in_array($m->getName(), $reserved))
			{
				$doc         = AnnotationParser::parse($m->getDocComment());
				$httpMethod  = $doc->getFirstAnnotation('httpMethod');
				$virtualPath = $doc->getFirstAnnotation('path');

				if(!empty($virtualPath) && $httpMethod == $requestMethod)
				{
					$match       = true;
					$virtualPath = trim($virtualPath, '/');

					if(empty($virtualPath))
					{
						if($rootMethod === false)
						{
							// we have an / path wich we will use if we find 
							// no other fitting path
							$rootMethod = $m;

							// if we have an root method an the real path is 
							// empty use the root method
							if(empty($realPath))
							{
								break;
							}
						}

						$match = false;
					}
					else
					{
						$virtualPath = explode('/', $virtualPath);

						foreach($virtualPath as $k => $fragment)
						{
							if(!empty($fragment))
							{
								if($fragment[0] == '{')
								{
									$key = trim($fragment, '{}');

									$uriFragments[$key] = isset($realPath[$k]) ? $realPath[$k] : null;									
								}
								else if(isset($realPath[$k]) && strcasecmp($realPath[$k], $fragment) == 0)
								{
								}
								else
								{
									$match = false;
									break;
								}
							}
							else
							{
								$match = false;
								break;
							}
						}
					}

					if($match)
					{
						$method = $m;
						break;
					}
				}
			}
		}

		// if we have an root method
		if($method === false && $rootMethod !== false)
		{
			$method = $rootMethod;
		}

		// if we have no method look for an index
		if($method === false && $class->hasMethod('__index'))
		{
			$method = $class->getMethod('__index');
		}

		return $method;
	}
}
