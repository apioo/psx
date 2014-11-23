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
 * ViewCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ViewCommand extends GenerateCommandAbstract
{
	protected function configure()
	{
		$this
			->setName('generate:view')
			->setDescription('Generates a new view controller and an template sample')
			->addArgument('namespace', InputArgument::REQUIRED, 'Absolute class name of the controller (i.e. Acme\News\Overview)')
			->addArgument('services', InputArgument::OPTIONAL, 'Comma seperated list of service ids (i.e. connection,template)', 'connection,template')
			->addOption('dry-run', null, InputOption::VALUE_NONE, 'Executes no file operations if true');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$definition = $this->getServiceDefinition($input);

		$output->writeln('Generating view controller');

		// create dir
		$path            = $definition->getPath();
		$applicationPath = $path . DIRECTORY_SEPARATOR . 'Application';
		$resourcePath    = $path . DIRECTORY_SEPARATOR . 'Resource';

		if(!is_dir($applicationPath))
		{
			$output->writeln('Create dir ' . $applicationPath);

			if(!$definition->isDryRun())
			{
				mkdir($applicationPath, 0744, true);
			}
		}

		if(!is_dir($resourcePath))
		{
			$output->writeln('Create dir ' . $resourcePath);

			if(!$definition->isDryRun())
			{
				mkdir($resourcePath, 0744, true);
			}
		}

		// generate controller
		$controllerFile = $applicationPath . DIRECTORY_SEPARATOR . $definition->getClassName() . '.php';
		$templateFile   = $resourcePath . DIRECTORY_SEPARATOR . $this->underscore($definition->getClassName()) . '.html';

		if(!is_file($controllerFile) && !is_file($templateFile))
		{
			$definition->setNamespace($definition->getNamespace() . '\Application');

			$source = $this->getControllerSource($definition);

			$output->writeln('Write file ' . $controllerFile);

			if(!$definition->isDryRun())
			{
				file_put_contents($controllerFile, $source);
			}

			// template file
			$source = $this->getTemplateSource();

			$output->writeln('Write file ' . $templateFile);

			if(!$definition->isDryRun())
			{
				file_put_contents($templateFile, $source);
			}
		}
		else
		{
			if(is_file($controllerFile))
			{
				throw new \RuntimeException('File ' . $controllerFile . ' already exists');
			}
			else if(is_file($templateFile))
			{
				throw new \RuntimeException('File ' . $templateFile . ' already exists');
			}
		}
	}

	protected function getControllerSource(ServiceDefinition $definition)
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

use PSX\Controller\ViewAbstract;

/**
 * {$className}
 *
 * @see http://phpsx.org/doc/design/controller.html
 */
class {$className} extends ViewAbstract
{
	{$services}

	public function doIndex()
	{
		// @TODO controller action

		\$this->setBody(array(
			'message' => 'This is the default controller of PSX',
		));
	}
}

PHP;
	}

	protected function getTemplateSource()
	{
		return <<<'HTML'
<!DOCTYPE>
<html>
<head>
	<title>Controller</title>
</head>
<body>

<p><?php echo $message; ?></p>

</body>
</html>
HTML;
	}
}
