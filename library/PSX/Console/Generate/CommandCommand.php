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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CommandCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CommandCommand extends GenerateCommandAbstract
{
	protected function configure()
	{
		$this
			->setName('generate:command')
			->setDescription('Generates a new command')
			->addArgument('namespace', InputArgument::REQUIRED, 'Absolute class name of the command (i.e. Acme\News\Overview)')
			->addArgument('services', InputArgument::OPTIONAL, 'Comma seperated list of service ids (i.e. connection,http)')
			->addOption('dry-run', null, InputOption::VALUE_NONE, 'Executes no file operations if true');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$definition = $this->getServiceDefinition($input);

		$output->writeln('Generating command');

		// create dir
		$path = $definition->getPath();

		if(!is_dir($path))
		{
			$output->writeln('Create dir ' . $path);

			if(!$definition->isDryRun())
			{
				mkdir($path, 0744, true);
			}
		}

		// generate controller
		$file = $path . DIRECTORY_SEPARATOR . $definition->getClassName() . '.php';

		if(!is_file($file))
		{
			$source = $this->getCommandSource($definition);

			$output->writeln('Write file ' . $file);

			if(!$definition->isDryRun())
			{
				file_put_contents($file, $source);
			}
		}
		else
		{
			throw new \RuntimeException('File ' . $file . ' already exists');
		}
	}

	protected function getCommandSource(ServiceDefinition $definition)
	{
		$namespace = $definition->getNamespace();
		$className = $definition->getClassName();
		$services  = '';

		foreach($definition->getServices() as $serviceName)
		{
			$services.= $this->getServiceSource($serviceName) . "\n\n";
		}

		$services = trim($services);

		return <<<PHP
<?php

namespace {$namespace};

use PSX\CommandAbstract;
use PSX\Command\Parameter;
use PSX\Command\Parameters;
use PSX\Command\OutputInterface;

/**
 * {$className}
 *
 * @see http://phpsx.org/doc/design/command.html
 */
class {$className} extends CommandAbstract
{
	{$services}

	public function onExecute(Parameters \$parameters, OutputInterface \$output)
	{
		\$foo = \$parameters->get('foo');
		\$bar = \$parameters->get('bar');

		// @TODO do a task

		\$output->writeln('This is an PSX sample command');
	}

	public function getParameters()
	{
		return \$this->getParameterBuilder()
			->setDescription('Displays informations about an foo command')
			->addOption('foo', Parameter::TYPE_REQUIRED, 'The foo parameter')
			->addOption('bar', Parameter::TYPE_OPTIONAL, 'The bar parameter')
			->getParameters();
	}
}

PHP;
	}
}
