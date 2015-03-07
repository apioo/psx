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

namespace PSX\Dependency;

use PSX\Api;
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
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
		return new Loader($this->get('loader_location_finder'), $this->get('loader_callback_resolver'), $this->get('event_dispatcher'), $this->get('logger'));
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
		return new Dispatch($this->get('config'), $this->get('loader'), $this->get('application_stack_factory'), $this->get('dispatch_sender'), $this->get('event_dispatcher'));
	}

	/**
	 * @return PSX\Loader\RoutingParserInterface
	 */
	public function getRoutingParser()
	{
		return new Loader\RoutingParser\RoutingFile($this->get('config')->get('psx_routing'));
	}

	/**
	 * @return PSX\Loader\ReverseRouter
	 */
	public function getReverseRouter()
	{
		return new Loader\ReverseRouter($this->get('routing_parser'), $this->get('config')->get('psx_url'), $this->get('config')->get('psx_dispatch'));
	}

	/**
	 * @return PSX\Api\ResourceListing
	 */
	public function getResourceListing()
	{
		return new Api\ResourceListing($this->get('routing_parser'), $this->get('controller_factory'));
	}
}
