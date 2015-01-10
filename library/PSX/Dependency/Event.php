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

namespace PSX\Dependency;

use PSX\DisplayException;
use PSX\Event as EventName;
use PSX\Event\RequestIncomingEvent;
use PSX\Event\RouteMatchedEvent;
use PSX\Event\ControllerExecuteEvent;
use PSX\Event\ControllerProcessedEvent;
use PSX\Event\ResponseSendEvent;
use PSX\Event\ExceptionThrownEvent;
use PSX\Loader\Location;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Event
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait Event
{
	/**
	 * @return Symfony\Component\EventDispatcher\EventDispatcherInterface
	 */
	public function getEventDispatcher()
	{
		$eventDispatcher = new EventDispatcher();

		$this->appendDefaultListener($eventDispatcher);

		return $eventDispatcher;
	}

	protected function appendDefaultListener(EventDispatcherInterface $eventDispatcher)
	{
		$logger = $this->get('logger');

		$eventDispatcher->addListener(EventName::REQUEST_INCOMING, function(RequestIncomingEvent $event) use ($logger){

			$logger->info('Incoming request ' . $event->getRequest()->getMethod() . ' ' . $event->getRequest()->getUrl());

		});

		$eventDispatcher->addListener(EventName::ROUTE_MATCHED, function(RouteMatchedEvent $event) use ($logger){

			$path       = '/' . ltrim($event->getPath(), '/');
			$controller = $event->getLocation()->getParameter(Location::KEY_SOURCE);

			$logger->info('Route matched ' . $event->getRequestMethod() . ' ' . $path . ' -> ' . $controller);

		});

		$eventDispatcher->addListener(EventName::CONTROLLER_EXECUTE, function(ControllerExecuteEvent $event) use ($logger){

			$controller = get_class($event->getController());

			$logger->info('Controller execute ' . $controller);

		});

		$eventDispatcher->addListener(EventName::CONTROLLER_PROCESSED, function(ControllerProcessedEvent $event) use ($logger){

			$controller = get_class($event->getController());

			$logger->info('Controller processed ' . $controller);

		});

		$eventDispatcher->addListener(EventName::RESPONSE_SEND, function(ResponseSendEvent $event) use ($logger){

			$logger->info('Send response');

		});

		$eventDispatcher->addListener(EventName::EXCEPTION_THROWN, function(ExceptionThrownEvent $event) use ($logger){

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
				$logger->notice($exception->getMessage(), $context);
			}
			else
			{
				$logger->error($exception->getMessage(), $context);
			}

		});
	}
}
