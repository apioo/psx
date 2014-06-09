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

use PSX\Command\ParameterBuilder;
use PSX\Command\Parameters;
use PSX\Loader\Location;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CommandAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class CommandAbstract implements CommandInterface
{
	protected $container;
	protected $location;
	protected $config;

	public function __construct(ContainerInterface $container, Location $location)
	{
		$this->container = $container;
		$this->location  = $location;
		$this->config    = $container->get('config');
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

		throw new BadMethodCallException('Call to undefined method ' . $name);
	}

	public function getParameters()
	{
		return new Parameters();
	}

	/**
	 * @return Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected function getContainer()
	{
		return $this->container;
	}

	/**
	 * @return PSX\Loader\Location
	 */
	protected function getLocation()
	{
		return $this->location;
	}

	/**
	 * @return PSX\Config
	 */
	protected function getConfig()
	{
		return $this->config;
	}

	protected function getParameterBuilder()
	{
		return new ParameterBuilder();
	}
}
