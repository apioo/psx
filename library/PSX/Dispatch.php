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

use DOMDocument;
use PSX\Base;
use PSX\Dispatch\ControllerFactoryInterface;
use PSX\Dispatch\SenderInterface;
use PSX\Dispatch\RedirectException;
use PSX\Event\Context\ControllerContext;
use PSX\Event\ExceptionThrownEvent;
use PSX\Event\RequestIncomingEvent;
use PSX\Event\ResponseSendEvent;
use PSX\Http;
use PSX\Http\Authentication;
use PSX\Http\Exception as StatusCode;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\StringStream;
use PSX\Loader\Context;
use PSX\Url;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * The dispatcher routes the request to the fitting controller. The route method
 * contains the global try catch for the application
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Dispatch
{
	protected $config;
	protected $loader;
	protected $sender;
	protected $factory;
	protected $eventDispatcher;

	public function __construct(Config $config, LoaderInterface $loader, ControllerFactoryInterface $factory, SenderInterface $sender, EventDispatcherInterface $eventDispatcher)
	{
		$this->config          = $config;
		$this->loader          = $loader;
		$this->sender          = $sender;
		$this->factory         = $factory;
		$this->eventDispatcher = $eventDispatcher;
	}

	public function route(RequestInterface $request, ResponseInterface $response, Context $context = null)
	{
		$this->eventDispatcher->dispatch(Event::REQUEST_INCOMING, new RequestIncomingEvent($request));

		// load controller
		$context = $context ?: new Context();

		try
		{
			$this->loader->load($request, $response, $context);
		}
		catch(StatusCode\NotModifiedException $e)
		{
			$response->setStatus($e->getStatusCode());
			$response->setBody(new StringStream());
		}
		catch(StatusCode\RedirectionException $e)
		{
			$response->setStatus($e->getStatusCode());
			$response->setHeader('Location', $e->getLocation());
			$response->setBody(new StringStream());
		}
		catch(\Exception $e)
		{
			$this->eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));

			if($e instanceof StatusCode\StatusCodeException)
			{
				$this->handleHttpStatusCodeException($e, $response);
			}
			else if($response->getStatusCode() == null)
			{
				if(isset(Http::$codes[$e->getCode()]))
				{
					$response->setStatus($e->getCode());
				}
				else
				{
					$response->setStatus(500);
				}
			}

			$context->set(Context::KEY_EXCEPTION, $e);

			$class      = isset($this->config['psx_error_controller']) ? $this->config['psx_error_controller'] : 'PSX\Controller\ErrorController';
			$controller = $this->factory->getController($class, $request, $response, $context);

			$this->loader->executeController($controller, $request, $response);
		}

		$this->eventDispatcher->dispatch(Event::RESPONSE_SEND, new ResponseSendEvent($response));

		// send response
		$this->sender->send($response);
	}

	protected function handleHttpStatusCodeException(StatusCode\StatusCodeException $e, ResponseInterface $response)
	{
		$response->setStatus($e->getStatusCode());

		if($e instanceof StatusCode\MethodNotAllowedException)
		{
			$allowedMethods = $e->getAllowedMethods();

			if(!empty($allowedMethods))
			{
				$response->setHeader('Allow', implode(', ', $allowedMethods));
			}
		}
		else if($e instanceof StatusCode\UnauthorizedException)
		{
			$type       = $e->getType();
			$parameters = $e->getParameters();

			if(!empty($type))
			{
				if(!empty($parameters))
				{
					$response->setHeader('WWW-Authenticate', $type . ' ' . Authentication::encodeParameters($parameters));
				}
				else
				{
					$response->setHeader('WWW-Authenticate', $type);
				}
			}
		}
	}
}
