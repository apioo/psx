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

namespace PSX\Console;

use PSX\Dependency\Container;
use PSX\Util\Annotation;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Displays all available services from the DI container. Note the getServiceIds
 * method is not defined in the interface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ContainerCommand extends Command
{
	protected $container;

	public function __construct(ContainerInterface $container)
	{
		parent::__construct();

		$this->container = $container;
	}

	protected function configure()
	{
		$this
			->setName('container')
			->setDescription('Displays all registered services');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$services  = $this->container->getServiceIds();
		$container = new ReflectionClass($this->container);
		$rows      = array();

		foreach($services as $serviceId)
		{
			try
			{
				$method = $container->getMethod('get' . Container::normalizeName($serviceId));
				$doc    = Annotation::parse($method->getDocComment());
				$return = $doc->getFirstAnnotation('return');

				if(!empty($return))
				{
					$definition = $return;
				}
				else
				{
					$definition = 'void';
				}

				$rows[] = array($serviceId, $definition);
			}
			catch(ReflectionException $e)
			{
				// method does not exist
			}
		}

		$table = new Table($output);
		$table
			->setStyle('compact')
			->setRows($rows);

		$table->render();
	}
}
