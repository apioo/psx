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

namespace PSX\Log;

use Psr\Log\LoggerInterface;
use PSX\DisplayException;
use PSX\Event as EventName;
use PSX\Event\RequestIncomingEvent;
use PSX\Event\RouteMatchedEvent;
use PSX\Event\ControllerExecuteEvent;
use PSX\Event\ControllerProcessedEvent;
use PSX\Event\ResponseSendEvent;
use PSX\Event\ExceptionThrownEvent;
use PSX\Http\Exception\StatusCodeException;
use PSX\Loader\Location;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * LogListener
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class LogListener implements EventSubscriberInterface
{
	protected $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function onRequestIncomming(RequestIncomingEvent $event)
	{
		$this->logger->info('Incoming request ' . $event->getRequest()->getMethod() . ' ' . $event->getRequest()->getUrl());
	}

	public function onRouteMatched(RouteMatchedEvent $event)
	{
		$path       = '/' . ltrim($event->getPath(), '/');
		$controller = $event->getLocation()->getParameter(Location::KEY_SOURCE);

		$this->logger->info('Route matched ' . $event->getRequestMethod() . ' ' . $path . ' -> ' . $controller);
	}

	public function onControllerExecute(ControllerExecuteEvent $event)
	{
		$controller = get_class($event->getController());

		$this->logger->info('Controller execute ' . $controller);
	}

	public function onControllerProcessed(ControllerProcessedEvent $event)
	{
		$controller = get_class($event->getController());

		$this->logger->info('Controller processed ' . $controller);
	}

	public function onResponseSend(ResponseSendEvent $event)
	{
		$this->logger->info('Send response');
	}

	public function onExceptionThrown(ExceptionThrownEvent $event)
	{
		$exception = $event->getException();
		$severity  = $exception instanceof \ErrorException ? $exception->getSeverity() : null;
		$context   = array(
			'file'     => $exception->getFile(),
			'line'     => $exception->getLine(),
			'trace'    => $exception->getTraceAsString(),
			'code'     => $exception->getCode(),
			'severity' => $severity,
		);

		if($exception instanceof DisplayException)
		{
			$this->logger->notice($exception->getMessage(), $context);
		}
		else if($exception instanceof StatusCodeException)
		{
			if($exception->isClientError())
			{
				$this->logger->notice($exception->getMessage(), $context);
			}
			else if($exception->isServerError())
			{
				$this->logger->error($exception->getMessage(), $context);
			}
			else
			{
				$this->logger->info($exception->getMessage(), $context);
			}
		}
		else
		{
			$this->logger->error($exception->getMessage(), $context);
		}
	}

	public static function getSubscribedEvents()
	{
		return array(
			EventName::REQUEST_INCOMING     => 'onRequestIncomming',
			EventName::ROUTE_MATCHED        => 'onRouteMatched',
			EventName::CONTROLLER_EXECUTE   => 'onControllerExecute',
			EventName::CONTROLLER_PROCESSED => 'onControllerProcessed',
			EventName::RESPONSE_SEND        => 'onResponseSend',
			EventName::EXCEPTION_THROWN     => 'onExceptionThrown',
		);
	}
}
