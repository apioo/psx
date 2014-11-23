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

namespace PSX\Console\Generate;

use PSX\Dependency\Container as PSXContainer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * GenerateCommandAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class GenerateCommandAbstract extends Command
{
	protected $container;

	public function __construct(ContainerInterface $container)
	{
		parent::__construct();

		$this->container = $container;
	}

	protected function getServiceDefinition(InputInterface $input)
	{
		$namespace = $input->getArgument('namespace');
		$services  = $input->getArgument('services');
		$dryRun    = $input->getOption('dry-run');

		$parts     = explode('\\', $namespace);
		$namespace = implode('\\', array_map('ucfirst', array_slice($parts, 0, count($parts) - 1)));
		$class     = end($parts);

		if(empty($namespace) || empty($class))
		{
			throw new \InvalidArgumentException('Namespace must have at least an vendor and class name i.e. Acme\News');
		}

		if(!empty($services))
		{
			$services = $this->getAvailableServices(explode(',', $services));
		}
		else
		{
			$services = array();
		}

		return new ServiceDefinition($namespace, $class, $services, $dryRun);
	}

	protected function getServiceSource($serviceId)
	{
		if($this->container instanceof PSXContainer)
		{
			$returnType = $this->container->getReturnType($serviceId);
		}
		else
		{
			$service = $this->container->get($serviceId);

			if(is_object($service))
			{
				$returnType = get_class($service);
			}
			else
			{
				$returnType = gettype($service);
			}
		}

		return <<<PHP
	/**
	 * @Inject
	 * @var {$returnType}
	 */
	protected \${$serviceId};
PHP;
	}

	protected function getAvailableServices(array $services)
	{
		$result = array();

		foreach($services as $service)
		{
			$service = trim($service);

			if($this->container->has($service))
			{
				$result[] = $service;
			}
			else
			{
				throw new \InvalidArgumentException('Given service ' . $service . ' not found');
			}
		}

		return $result;
	}

	protected function underscore($word)
	{
		return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $word));
	}
}
