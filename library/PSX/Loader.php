<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use PSX\Dispatch\FilterInterface;
use PSX\Event\ControllerExecuteEvent;
use PSX\Event\ControllerProcessedEvent;
use PSX\Event\RouteMatchedEvent;
use PSX\Loader\Callback;
use PSX\Loader\CallbackResolverInterface;
use PSX\Loader\Location;
use PSX\Loader\LocationFinderInterface;
use PSX\Loader\LocationFinder\RoutingFile;
use PSX\Loader\InvalidPathException;
use PSX\Dispatch\FilterChain;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
	const REQUEST_LOCATION = 'psx_request_location';
	const REQUEST_CALLBACK = 'psx_request_callback';

	protected $locationFinder;
	protected $callbackResolver;
	protected $eventDispatcher;
	protected $logger;

	protected $recursiveLoading = false;
	protected $loaded = array();

	public function __construct(LocationFinderInterface $locationFinder, CallbackResolverInterface $callbackResolver, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
	{
		$this->locationFinder   = $locationFinder;
		$this->callbackResolver = $callbackResolver;
		$this->eventDispatcher  = $eventDispatcher;
		$this->logger           = $logger;
	}

	public function setRecursiveLoading($recursiveLoading)
	{
		$this->recursiveLoading = $recursiveLoading;
	}

	/**
	 * Loads the location of the controller through the defined location finder. 
	 * Then uses the callback resolver to obtain an callback from the location
	 *
	 * @param Psr\Http\Message\RequestInterface $request
	 * @param Psr\Http\Message\ResponseInterface $response
	 * @return PSX\ControllerAbstract
	 */
	public function load(RequestInterface $request, ResponseInterface $response)
	{
		$path     = $request->getUrl()->getPath();
		$location = $this->locationFinder->resolve($request->getMethod(), $path);

		if($location instanceof Location)
		{
			$request->setAttribute(self::REQUEST_LOCATION, $location);

			$this->eventDispatcher->dispatch(Event::ROUTE_MATCHED, new RouteMatchedEvent($request->getMethod(), $path, $location));

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
			throw new InvalidPathException('Unkown location', $path);
		}
	}

	/**
	 * Loads an specific controller direct without any routing
	 *
	 * @param PSX\Loader\Callback $callback
	 * @param Psr\Http\Message\RequestInterface $request
	 * @param Psr\Http\Message\ResponseInterface $response
	 * @return PSX\ControllerAbstract
	 */
	public function loadClass(Callback $callback, RequestInterface $request, ResponseInterface $response)
	{
		return $this->runControllerLifecycle($callback, $request, $response);
	}

	protected function runControllerLifecycle(Callback $callback, RequestInterface $request, ResponseInterface $response)
	{
		$request->setAttribute(self::REQUEST_CALLBACK, $callback);

		$controller = $callback->getClass();

		if($controller instanceof ApplicationStackInterface)
		{
			$this->eventDispatcher->dispatch(Event::CONTROLLER_EXECUTE, new ControllerExecuteEvent($controller, $request, $response));

			$filterChain = new FilterChain($controller->getApplicationStack());
			$filterChain->setLogger($this->logger);
			$filterChain->handle($request, $response);

			$this->eventDispatcher->dispatch(Event::CONTROLLER_PROCESSED, new ControllerProcessedEvent($controller, $request, $response));

			return $controller;
		}
		else
		{
			throw new UnexpectedValueException('Controller must be an instance of PSX\ControllerInterface');
		}
	}
}
