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

use InvalidArgumentException;
use PSX\Dispatch\FilterInterface;
use PSX\Http\Request;
use PSX\Http\Response;
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

	protected $loaded = array();
	protected $routes = array();

	public function __construct(LocationFinderInterface $locationFinder, CallbackResolverInterface $callbackResolver)
	{
		$this->locationFinder   = $locationFinder;
		$this->callbackResolver = $callbackResolver;
	}

	/**
	 * Loads the location of the controller through the defined location finder. 
	 * Then calls the executor to create a new instance of the controller and
	 * call the fitting methods. Locations which gets resolved by the location 
	 * finder can only be loaded once
	 *
	 * @param PSX\Http\Request $request
	 * @param PSX\Http\Response $response
	 * @return PSX\ControllerAbstract
	 */
	public function load(Request $request, Response $response)
	{
		$path = $request->getUrl()->getPath();

		if(isset($this->routes[md5($path)]))
		{
			$path = $this->routes[md5($path)];
		}

		$location = $this->locationFinder->resolve($request->getMethod(), $path);

		if($location instanceof Location)
		{
			if(!in_array($location->getId(), $this->loaded))
			{
				$callback   = $this->callbackResolver->resolve($location, $request, $response);
				$controller = $this->runControllerLifecycle($callback, $request, $response);

				$this->loaded[] = $location->getId();

				return $controller;
			}
		}
		else
		{
			throw new InvalidPathException('Unkown module "' . $path . '"', 404);
		}
	}

	public function addRoute($sourcePath, $destPath)
	{
		$key = md5($destPath);

		$this->routes[$sourcePath] = $destPath;
	}

	public function getRoute($path)
	{
		$key = md5($path);

		return isset($this->routes[$key]) ? $this->routes[$key] : false;
	}

	protected function runControllerLifecycle($callback, Request $request, Response $response)
	{
		if(is_array($callback))
		{
			$controller = isset($callback[0]) ? $callback[0] : null;
			$method     = isset($callback[1]) ? $callback[1] : null;

			if($controller instanceof ControllerInterface)
			{
				// call request filter
				if($controller->getStage() & ControllerInterface::CALL_REQUEST_FILTER)
				{
					$filters = $controller->getRequestFilter();

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
						case 'GET':
							$controller->onGet();
							break;

						case 'POST':
							$controller->onPost();
							break;

						case 'PUT':
							$controller->onPut();
							break;

						case 'DELETE':
							$controller->onDelete();
							break;
					}
				}

				// call method if available
				if($controller->getStage() & ControllerInterface::CALL_METHOD)
				{
					if(!empty($method) && is_callable($callback))
					{
						call_user_func_array($callback, array($request, $response));
					}
				}

				// process response
				$controller->processResponse(null);

				// call response filter
				if($controller->getStage() & ControllerInterface::CALL_RESPONSE_FILTER)
				{
					$filters = $controller->getResponseFilter();

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
				throw new UnexpectedValueException('Invalid controller callback');
			}
		}
		else if(is_callable($callback))
		{
			call_user_func_array($callback, array($request, $response));
		}
		else
		{
			throw new UnexpectedValueException('Invalid controller callback');
		}
	}
}
