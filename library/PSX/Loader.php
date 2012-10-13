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
	protected $path;

	public function __construct(PSX_Base $base)
	{
		$this->base   = $base;
		$this->config = $base->getConfig();

		$this->loaded = array();
		$this->routes = array();
		$this->path   = $this->config['psx_path_module'];
	}

	public function load($path)
	{
		if(($rewritePath = $this->getRoute($path)) !== false)
		{
			$path = $rewritePath;
		}

		list($path, $file, $class, $method, $uriFragments) = $this->parsePath($path);

		if(!in_array($path, $this->loaded))
		{
			if($class->isSubclassOf('PSX_ModuleAbstract'))
			{
				$handle = $class->newInstance($this->base, $path, $uriFragments);
				$handle->_ini();

				$this->loaded[] = $path;

				if($handle instanceof PSX_Module_PrivateInterface)
				{
					// we dont call any method if the class is private
				}
				else
				{
					if(!empty($method))
					{
						$method = $class->getMethod($method);

						if(!$method->isStatic())
						{
							$method->invoke($handle);
						}
					}
				}

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

	public function setPath($path)
	{
		$this->path = $path;
	}

	public function getPath()
	{
		return $this->path;
	}

	/**
	 * URL format: index.php/[path/to/the/file]/class/[method][/foo/bar]
	 *
	 * The brackets in [] are optional. the [method] is only called when it is
	 * available. All values after the method are saved as uri fragment. You can
	 * access these values in your module with $this->getUriFragment() wich
	 * returns the values as an array where the values are exploded after '/'
	 */
	protected function parsePath($x, $deep = 0)
	{
		$x   = empty($x) ? $this->config['psx_module_default'] : $x;
		$x   = trim($x, '/');
		$fpc = $this->getFPC($x);

		if($fpc !== false)
		{
			list($file, $path, $class) = $fpc;

			require_once($file);

			$class = new ReflectionClass($class);

			// control whether the method exists or not
			$method   = false;
			$rest     = substr($x, strlen($path) + 1);
			$reserved = array('__construct', 'getDependencies', '_ini', 'onLoad', 'onGet', 'onPost', 'onPut', 'onDelete', 'processResponse');

			if(!empty($rest))
			{
				$method = self::getPart($rest);

				if(!in_array($method, $reserved) && $class->hasMethod($method))
				{
					$rest = substr($rest, strlen($method) + 1);
				}
				else
				{
					$method = false;
				}
			}

			// if we have no method look for an index
			if($method === false && $class->hasMethod('__index'))
			{
				$method = '__index';
			}

			// get uri fragments
			$uriFragments = array();

			if(!empty($rest))
			{
				$uriFragments = explode('/', trim($rest, '/'));
			}
		}
		else
		{
			if($deep == 0)
			{
				$x = $this->config['psx_module_default'] . '/' . $x;

				return $this->parsePath($x, ++$deep);
			}
			else
			{
				throw new PSX_Loader_Exception('Unkown module "' . $x . '" in ' . $this->path);
			}
		}

		return array(

			$path,
			$file,
			$class,
			$method,
			$uriFragments,

		);
	}

	protected function getFPC($path)
	{
		$path = trim($path, '/');

		if(is_file($this->path . '/' . $path . '.php'))
		{
			$file = $this->path . '/' . $path . '.php';
			$pos  = strrpos($path, '/');

			if($pos === false)
			{
				$class = $path;
			}
			else
			{
				$class = substr($path, $pos + 1);
			}

			return array($file, $path, $class);
		}
		elseif(is_file($this->path . '/' . $path . '/' . 'index.php'))
		{
			$file  = $this->path . '/' . $path . '/' . 'index.php';
			$class = 'index';

			return array($file, $path, $class);
		}
		else
		{
			$pos  = strrpos($path, '/');
			$file = substr($path, 0, $pos);

			if($pos === false)
			{
				return false;
			}
			else
			{
				return $this->getFPC($file);
			}
		}
	}

	public static function getPart($path)
	{
		$pos = strpos($path, '/');

		if($pos === false)
		{
			return $path;
		}
		else
		{
			return substr($path, 0, $pos);
		}
	}
}

