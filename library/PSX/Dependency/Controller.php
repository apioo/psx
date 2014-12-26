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

namespace PSX\Dependency;

use PSX\Dispatch;
use PSX\Dispatch\ApplicationStackFactory;
use PSX\Dispatch\ControllerFactory;
use PSX\Dispatch\RequestFactory;
use PSX\Dispatch\ResponseFactory;
use PSX\Dispatch\Sender\Basic as BasicSender;
use PSX\Loader;

/**
 * Controller
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait Controller
{
	/**
	 * @return PSX\Dispatch\ControllerFactoryInterface
	 */
	public function getApplicationStackFactory()
	{
		return new ApplicationStackFactory($this->get('object_builder'));
	}

	/**
	 * @return PSX\Dispatch\ControllerFactoryInterface
	 */
	public function getControllerFactory()
	{
		return new ControllerFactory($this->get('object_builder'));
	}

	/**
	 * @return PSX\Dispatch\SenderInterface
	 */
	public function getDispatchSender()
	{
		return new BasicSender();
	}

	/**
	 * @return PSX\Loader\LocationFinderInterface
	 */
	public function getLoaderLocationFinder()
	{
		return new Loader\LocationFinder\RoutingParser($this->get('routing_parser'));
	}

	/**
	 * @return PSX\Loader\CallbackResolverInterface
	 */
	public function getLoaderCallbackResolver()
	{
		return new Loader\CallbackResolver\DependencyInjector($this->get('application_stack_factory'));
	}

	/**
	 * @return PSX\Loader
	 */
	public function getLoader()
	{
		$loader = new Loader($this->get('loader_location_finder'), $this->get('loader_callback_resolver'), $this->get('event_dispatcher'));

		// configure loader
		//$loader->addRoute('.well-known/host-meta', 'foo');

		return $loader;
	}

	/**
	 * @return PSX\Dispatch\RequestFactoryInterface
	 */
	public function getRequestFactory()
	{
		return new RequestFactory($this->get('config'));
	}

	/**
	 * @return PSX\Dispatch\ResponseFactoryInterface
	 */
	public function getResponseFactory()
	{
		return new ResponseFactory();
	}

	/**
	 * @return PSX\Dispatch
	 */
	public function getDispatch()
	{
		return new Dispatch($this->get('config'), $this->get('loader'), $this->get('controller_factory'), $this->get('dispatch_sender'), $this->get('event_dispatcher'));
	}

	/**
	 * @return PSX\Loader\RoutingParserInterface
	 */
	public function getRoutingParser()
	{
		return new Loader\RoutingParser\RoutingFile($this->get('config')->get('psx_routing'));
	}

	/**
	 * @return Loader\ReverseRouter
	 */
	public function getReverseRouter()
	{
		return new Loader\ReverseRouter($this->get('routing_parser'), $this->get('config')->get('psx_url'), $this->get('config')->get('psx_dispatch'));
	}
}
