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
	protected function getServiceDefinition(InputInterface $input)
	{
		$namespace = $input->getArgument('namespace');
		$dryRun    = $input->getOption('dry-run');

		$parts     = explode('\\', $namespace);
		$namespace = implode('\\', array_map('ucfirst', array_slice($parts, 0, count($parts) - 1)));
		$class     = end($parts);

		if(empty($namespace) || empty($class))
		{
			throw new \InvalidArgumentException('Namespace must have at least an vendor and class name i.e. Acme\News');
		}

		return new ServiceDefinition($namespace, $class, $dryRun);
	}

	protected function makeDir($path)
	{
		return mkdir($path, 0744, true);
	}

	protected function writeFile($file, $content)
	{
		return file_put_contents($file, $content);
	}
}
