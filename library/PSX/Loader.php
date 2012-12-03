<?php
/*
 *  $Id: Loader.php 658 2012-10-06 22:39:32Z k42b3.x@googlemail.com $
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

/**
 * Class to load modules of psx.
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Loader
 * @version    $Revision: 658 $
 */
class PSX_Loader
{
	protected $base;
	protected $config;

	protected $loaded;
	protected $routes;
	protected $default;

	protected $locationFinder;

	public function __construct(PSX_Base $base)
	{
		$this->base    = $base;
		$this->config  = $base->getConfig();

		$this->loaded  = array();
		$this->routes  = array();
		$this->default = $this->config['psx_module_default'];
	}

	public function load($path)
	{
		list($location, $method, $uriFragments) = $this->resolvePath($path);

		if(!in_array($location->getId(), $this->loaded))
		{
			$class = $location->getClass();

			if($class->isSubclassOf('PSX_ModuleAbstract'))
			{
				$handle = $class->newInstance($location, $this->base, $path, $uriFragments);
				$handle->_ini();

				if($method instanceof ReflectionMethod)
				{
					$method->invoke($handle);
				}

				$this->loaded[] = $location->getId();

				return $handle;
			}
			else
			{
				throw new PSX_Loader_Exception('Class is not an instance of PSX_ModuleAbstract');
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

	public function setDefault($default)
	{
		$this->default = $default;
	}

	public function getDefault()
	{
		return $this->default;
	}

	/**
	 * Sets the strategy howto resolve a path to an location. If no strategy 
	 * is set the filesystem location finder weill be used.
	 *
	 * @param PSX_Loader_LocationFinderInterface $locationFinder
	 * @return void
	 */
	public function setLocationFinder(PSX_Loader_LocationFinderInterface $locationFinder)
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
	 * @param integer $deep
	 * @return array
	 */
	protected function resolvePath($x, $deep = 0)
	{
		if(($rewritePath = $this->getRoute($x)) !== false)
		{
			$x = $rewritePath;
		}

		$x = trim($x, '/');

		if($this->locationFinder === null)
		{
			$this->locationFinder = new PSX_Loader_LocationFinder_FileSystem($this->config['psx_path_module']);
		}

		$location     = $this->locationFinder->resolve($x);
		$uriFragments = array();

		if($location instanceof PSX_Loader_Location)
		{
			$method = $this->getMethodToCall($location->getClass(), $location->getPath(), $uriFragments);
		}
		else
		{
			if($deep == 0)
			{
				$x = $this->default . '/' . $x;

				return $this->resolvePath($x, ++$deep);
			}
			else
			{
				throw new PSX_Loader_Exception('Unkown module "' . $x . '"');
			}
		}

		return array(
			$location,
			$method,
			$uriFragments,
		);
	}

	protected function getMethodToCall(ReflectionClass $class, $path, &$uriFragments)
	{
		// search method wich sould be called
		$method     = false;
		$rootMethod = false;

		$realPath = trim($path, '/');
		$reserved = array('__construct', 'getDependencies', '_ini', 'onLoad', 'onGet', 'onPost', 'onPut', 'onDelete', 'processResponse');
		$methods  = $class->getMethods();

		if(!empty($realPath))
		{
			$realPath = explode('/', $realPath);
		}
		else
		{
			$realPath = null;
		}

		foreach($methods as $m)
		{
			if($m->isPublic() && !in_array($m->getName(), $reserved))
			{
				$doc         = PSX_Util_Annotation::parse($m->getDocComment());
				$httpMethod  = $doc->getFirstAnnotation('httpMethod');
				$virtualPath = $doc->getFirstAnnotation('path');

				if(!empty($virtualPath) && $httpMethod == PSX_Base::getRequestMethod())
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
							if(isset($realPath[$k]) && !empty($fragment))
							{
								if($fragment[0] == '{')
								{
									$key = trim($fragment, '{}');

									$uriFragments[$key] = $realPath[$k];
								}
								else if(strcasecmp($realPath[$k], $fragment) == 0)
								{
								}
								else
								{
									$match = false;
									break;
								}
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
