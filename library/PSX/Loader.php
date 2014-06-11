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

namespace PSX;

use InvalidArgumentException;
use Psr\HttpMessage\RequestInterface;
use Psr\HttpMessage\ResponseInterface;
use PSX\Dispatch\FilterInterface;
use PSX\Loader\Callback;
use PSX\Loader\CallbackResolverInterface;
use PSX\Loader\Location;
use PSX\Loader\LocationFinderInterface;
use PSX\Loader\LocationFinder\RoutingFile;
use PSX\Loader\InvalidPathException;
use UnexpectedValueException;

/**
 * Loader
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Loader implements LoaderInterface
{
	protected $locationFinder;
	protected $callbackResolver;
	protected $recursiveLoading;

	protected $loaded = array();
	protected $routes = array();

	public function __construct(LocationFinderInterface $locationFinder, CallbackResolverInterface $callbackResolver)
	{
		$this->locationFinder   = $locationFinder;
		$this->callbackResolver = $callbackResolver;
		$this->recursiveLoading = false;
	}

	public function setRecursiveLoading($recursiveLoading)
	{
		$this->recursiveLoading = $recursiveLoading;
	}

	/**
	 * Loads the location of the controller through the defined location finder. 
	 * Then uses the callback resolver to obtain an callback from the location
	 *
	 * @param Psr\HttpMessage\RequestInterface $request
	 * @param Psr\HttpMessage\ResponseInterface $response
	 * @return PSX\ControllerAbstract
	 */
	public function load(RequestInterface $request, ResponseInterface $response)
	{
		$path = $request->getUrl()->getPath();

		if(isset($this->routes[md5($path)]))
		{
			$path = $this->routes[md5($path)];
		}

		$location = $this->locationFinder->resolve($request->getMethod(), $path);

		if($location instanceof Location)
		{
			$callback = $this->callbackResolver->resolve($location, $request, $response);
			$id       = spl_object_hash($callback->getClass());

			if($this->recursiveLoading || !in_array($id, $this->loaded))
			{
				$controller = $this->runControllerLifecycle($callback, $request, $response);

				$this->loaded[] = $id;
			}
			else
			{
				$controller = $callback->getClass();
			}

			return $controller;
		}
		else
		{
			throw new InvalidPathException('Unkown module "' . $path . '"', 404);
		}
	}

	/**
	 * Loads an specific controller direct without any routing
	 *
	 * @param PSX\Loader\Callback $callback
	 * @param Psr\HttpMessage\RequestInterface $request
	 * @param Psr\HttpMessage\ResponseInterface $response
	 * @return PSX\ControllerAbstract
	 */
	public function loadClass(Callback $callback, RequestInterface $request, ResponseInterface $response)
	{
		return $this->runControllerLifecycle($callback, $request, $response);
	}

	public function addRoute($sourcePath, $destPath)
	{
		$key = md5($sourcePath);

		$this->routes[$key] = $destPath;
	}

	public function getRoute($path)
	{
		$key = md5($path);

		return isset($this->routes[$key]) ? $this->routes[$key] : null;
	}

	protected function runControllerLifecycle(Callback $callback, RequestInterface $request, ResponseInterface $response)
	{
		$controller = $callback->getClass();
		$method     = $callback->getMethod();

		if($controller instanceof ControllerInterface)
		{
			// call pre filter
			if($controller->getStage() & ControllerInterface::CALL_PRE_FILTER)
			{
				$filters = $controller->getPreFilter();

				foreach($filters as $filter)
				{
					if($filter instanceof FilterInterface)
					{
						$filter->handle($request, $response);
					}
					else if(is_callable($filter))
					{
						call_user_func_array($filter, array($request, $response));
					}
					else
					{
						throw new Exception('Invalid request filter');
					}
				}
			}

			// call onload method
			if($controller->getStage() & ControllerInterface::CALL_ONLOAD)
			{
				$controller->onLoad();
			}

			// call request method
			if($controller->getStage() & ControllerInterface::CALL_REQUEST_METHOD)
			{
				switch($request->getMethod())
				{
					case 'DELETE':
						$controller->onDelete();
						break;

					case 'GET':
						$controller->onGet();
						break;

					case 'HEAD':
						$controller->onHead();
						break;

					case 'POST':
						$controller->onPost();
						break;

					case 'PUT':
						$controller->onPut();
						break;
				}
			}

			// call method if available
			if($controller->getStage() & ControllerInterface::CALL_METHOD)
			{
				$method = $callback->getMethod();

				if(!empty($method))
				{
					call_user_func_array(array($controller, $method), $callback->getParameters());
				}
			}

			// process response
			$controller->processResponse(null);

			// call post filter
			if($controller->getStage() & ControllerInterface::CALL_POST_FILTER)
			{
				$filters = $controller->getPostFilter();

				foreach($filters as $filter)
				{
					if($filter instanceof FilterInterface)
					{
						$filter->handle($request, $response);
					}
					else if(is_callable($filter))
					{
						call_user_func_array($filter, array($request, $response));
					}
					else
					{
						throw new Exception('Invalid response filter');
					}
				}
			}

			return $controller;
		}
		else
		{
			throw new UnexpectedValueException('Controller must be an instance of PSX\ControllerInterface');
		}
	}
}
