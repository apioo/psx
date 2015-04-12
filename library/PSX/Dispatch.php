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
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Dispatch
{
	protected $config;
	protected $loader;
	protected $sender;
	protected $factory;
	protected $eventDispatcher;
	protected $exceptionConverter;

	public function __construct(Config $config, LoaderInterface $loader, ControllerFactoryInterface $factory, SenderInterface $sender, EventDispatcherInterface $eventDispatcher, Exception\Converter $exceptionConverter)
	{
		$this->config             = $config;
		$this->loader             = $loader;
		$this->sender             = $sender;
		$this->factory            = $factory;
		$this->eventDispatcher    = $eventDispatcher;
		$this->exceptionConverter = $exceptionConverter;
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

			$this->handleException($e, $response);

			try
			{
				$context->set(Context::KEY_EXCEPTION, $e);

				$class      = isset($this->config['psx_error_controller']) ? $this->config['psx_error_controller'] : 'PSX\Controller\ErrorController';
				$controller = $this->factory->getController($class, $request, $response, $context);

				$this->loader->executeController($controller, $request, $response);
			}
			catch(\Exception $e)
			{
				// in this case the error controller has thrown an exception. 
				// This can happen i.e. if we can not represent the error in an
				// fitting media type. In this case we send json to the client

				$this->handleException($e, $response);

				$record = $this->exceptionConverter->convert($e);

				$response->setHeader('Content-Type', 'application/json');
				$response->setBody(new StringStream(Json::encode($record->getRecordInfo()->getData(), JSON_PRETTY_PRINT)));
			}
		}

		$this->eventDispatcher->dispatch(Event::RESPONSE_SEND, new ResponseSendEvent($response));

		// send response
		$this->sender->send($response);
	}

	protected function handleException(\Exception $e, ResponseInterface $response)
	{
		if($e instanceof StatusCode\StatusCodeException)
		{
			$this->handleStatusCodeException($e, $response);
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
	}

	protected function handleStatusCodeException(StatusCode\StatusCodeException $e, ResponseInterface $response)
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

	protected function get()
	{
		
	}
}
