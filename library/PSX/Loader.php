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

namespace PSX;

use PSX\Dispatch\RequestFilterInterface;
use PSX\Http\Request;
use PSX\Loader\Location;
use PSX\Loader\LocationFinderInterface;
use PSX\Loader\LocationFinder\RoutingFile;
use PSX\Util\Annotation;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class to load modules of psx.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Loader
{
	protected $base;
	protected $config;
	protected $container;

	protected $loaded;
	protected $routes;

	protected $locationFinder;

	public function __construct(ContainerInterface $container)
	{
		$this->config    = $container->get('config');
		$this->container = $container;

		$this->loaded  = array();
		$this->routes  = array();
	}

	public function load($path, Request $request)
	{
		list($location, $method, $uriFragments) = $this->resolvePath($path, $request);

		if(!in_array($location->getId(), $this->loaded))
		{
			$class = $location->getClass();

			if($class->isSubclassOf('\PSX\ModuleAbstract'))
			{
				// create controller
				$handle = $class->newInstance($this->container, $location, $path, $uriFragments, $request);

				// call request filter
				if($handle->getStage() & ModuleAbstract::CALL_REQUEST_FILTER)
				{
					$filters = $handle->getRequestFilter();

					foreach($filters as $filter)
					{
						if($filter instanceof RequestFilterInterface)
						{
							$filter->handle($request);
						}
						else if(is_callable($filter))
						{
							call_user_func_array($filter, array($request));
						}
						else
						{
							throw new Exception('Invalid request filter');
						}
					}
				}

				// call onload method
				if($handle->getStage() & ModuleAbstract::CALL_ONLOAD)
				{
					$handle->onLoad();
				}

				// call request method
				if($handle->getStage() & ModuleAbstract::CALL_REQUEST_METHOD)
				{
					switch(Base::getRequestMethod())
					{
						case 'GET':
							$handle->onGet();
							break;

						case 'POST':
							$handle->onPost();
							break;

						case 'PUT':
							$handle->onPut();
							break;

						case 'DELETE':
							$handle->onDelete();
							break;
					}
				}

				// call method if available
				if($handle->getStage() & ModuleAbstract::CALL_METHOD)
				{
					if($method instanceof ReflectionMethod)
					{
						$method->invoke($handle);
					}
				}


				$this->loaded[] = $location->getId();

				return $handle;
			}
			else
			{
				throw new Exception('Class is not an instance of PSX\ModuleAbstract');
			}
		}

		return false;
	}

	public function addRoute($path, $module)
	{
		$key = md5($path);

		$this->routes[$key] = $module;
	}

	public function getRoute($path)
	{
		$key = md5($path);

		return isset($this->routes[$key]) ? $this->routes[$key] : false;
	}

	/**
	 * Sets the strategy howto resolve a path to an location. If no strategy 
	 * is set the filesystem location finder weill be used.
	 *
	 * @param PSX\Loader\LocationFinderInterface $locationFinder
	 * @return void
	 */
	public function setLocationFinder(LocationFinderInterface $locationFinder)
	{
		$this->locationFinder = $locationFinder;
	}

	/**
	 * If a method in the module has an docblock containing @httpMethod and
	 * @path parameter the loader will call the method depending on the virtual 
	 * path and the request method. I.e. if the virtual path is /foo/1 this will 
	 * match if the path parameter is /foo/{bar}. You can access the values in 
	 * the curly brackets with $this->uriFragments['bar'].
	 *
	 * @param string $x
	 * @param PSX\Http\Request $request
	 * @return array
	 */
	protected function resolvePath($x, Request $request)
	{
		if(($rewritePath = $this->getRoute($x)) !== false)
		{
			$x = $rewritePath;
		}

		$x = trim($x, '/');

		if($this->locationFinder === null)
		{
			$this->locationFinder = new RoutingFile($this->config['psx_routing']);
		}

		$location     = $this->locationFinder->resolve($x);
		$method       = null;
		$uriFragments = array();

		if($location instanceof Location)
		{
			$method = $this->getMethodToCall($location->getClass(), $location->getPath(), $uriFragments, $request);
		}
		else
		{
			throw new Exception('Unkown module "' . $x . '"', 404);
		}

		return array(
			$location,
			$method,
			$uriFragments,
		);
	}

	protected function getMethodToCall(ReflectionClass $class, $path, &$uriFragments, Request $request)
	{
		// search method wich sould be called
		$method     = false;
		$rootMethod = false;

		$realPath = trim($path, '/');
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
				$doc         = Annotation::parse($m->getDocComment());
				$httpMethod  = $doc->getFirstAnnotation('httpMethod');
				$virtualPath = $doc->getFirstAnnotation('path');

				if(!empty($virtualPath) && $httpMethod == $request->getMethod())
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
