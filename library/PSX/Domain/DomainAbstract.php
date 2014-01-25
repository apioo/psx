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

namespace PSX\Domain;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * DomainAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class DomainAbstract implements ContainerAwareInterface, EventDispatcherInterface
{
	protected $container;

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	public function dispatch($eventName, Event $event = null)
	{
		$this->container->get('event_dispatcher')->dispatch($eventName, $event);
	}

	public function getListeners($eventName = null)
	{
		return $this->container->get('event_dispatcher')->getListeners($eventName);
	}

	public function hasListeners($eventName = null)
	{
		return $this->container->get('event_dispatcher')->hasListeners($eventName);
	}

	public function addListener($eventName, $listener, $priority = 0)
	{
		$this->container->get('event_dispatcher')->addListener($eventName, $listener, $priority);
	}

	public function removeListener($eventName, $listener)
	{
		$this->container->get('event_dispatcher')->removeListener($eventName, $listener);
	}

	public function addSubscriber(EventSubscriberInterface $subscriber)
	{
		$this->container->get('event_dispatcher')->addSubscriber($subscriber);
	}

	public function removeSubscriber(EventSubscriberInterface $subscriber)
	{
		$this->container->get('event_dispatcher')->removeSubscriber($subscriber);
	}

	/**
	 * If the called method starts with "get" the matching service from the di 
	 * container is returned else null
	 *
	 * @return object
	 */
	public function __call($name, $args)
	{
		if(substr($name, 0, 3) == 'get')
		{
			$service = lcfirst(substr($name, 3));

			if($this->container->has($service))
			{
				return $this->container->get($service);
			}

			throw new InvalidArgumentException('Service ' . $service . ' not available');
		}
	}
}
