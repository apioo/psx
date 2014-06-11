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

use DOMDocument;
use Psr\HttpMessage\RequestInterface;
use Psr\HttpMessage\ResponseInterface;
use PSX\Base;
use PSX\Dispatch\ControllerFactoryInterface;
use PSX\Dispatch\SenderInterface;
use PSX\Dispatch\RedirectException;
use PSX\Loader\Callback;
use PSX\Loader\Location;

/**
 * The dispatcher routes the request to the fitting controller. The route method
 * contains the global try catch for the application
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Dispatch extends \Exception
{
	protected $config;
	protected $loader;
	protected $sender;
	protected $factory;

	public function __construct(Config $config, LoaderInterface $loader, ControllerFactoryInterface $factory, SenderInterface $sender)
	{
		$this->config  = $config;
		$this->loader  = $loader;
		$this->sender  = $sender;
		$this->factory = $factory;
	}

	public function route(RequestInterface $request, ResponseInterface $response)
	{
		// load controller
		try
		{
			$this->loader->load($request, $response);
		}
		catch(RedirectException $e)
		{
			$response->setStatusCode($e->getStatusCode());
			$response->setHeader('Location', $e->getUrl());
		}
		catch(\Exception $e)
		{
			$class    = isset($this->config['psx_error_controller']) ? $this->config['psx_error_controller'] : 'PSX\Controller\GenericErrorController';
			$location = new Location();
			$location->setParameter(Location::KEY_EXCEPTION, $e);

			$controller = $this->factory->getController($class, $location, $request, $response);
			$callback   = new Callback($controller, null);

			$this->loader->loadClass($callback, $request, $response);
		}

		// send response
		$this->sender->send($response);
	}
}
