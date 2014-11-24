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
 * ApiCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ApiCommand extends ContainerGenerateCommandAbstract
{
	protected function configure()
	{
		$this
			->setName('generate:api')
			->setDescription('Generates a new api controller')
			->addArgument('namespace', InputArgument::REQUIRED, 'Absolute class name of the controller (i.e. Acme\News\Overview)')
			->addArgument('services', InputArgument::OPTIONAL, 'Comma seperated list of service ids (i.e. connection,schemaManager)', 'connection,schemaManager')
			->addOption('dry-run', null, InputOption::VALUE_NONE, 'Executes no file operations if true');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$definition = $this->getServiceDefinition($input);

		$output->writeln('Generating api controller');

		// create dir
		$path = $definition->getPath();

		if(!is_dir($path))
		{
			$output->writeln('Create dir ' . $path);

			if(!$definition->isDryRun())
			{
				$this->makeDir($path);
			}
		}

		// generate controller
		$file = $path . DIRECTORY_SEPARATOR . $definition->getClassName() . '.php';

		if(!is_file($file))
		{
			$source = $this->getControllerSource($definition);

			$output->writeln('Write file ' . $file);

			if(!$definition->isDryRun())
			{
				$this->writeFile($file, $source);
			}
		}
		else
		{
			throw new \RuntimeException('File ' . $file . ' already exists');
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

use PSX\Controller\SchemaApiAbstract;
use PSX\Api\Documentation;
use PSX\Api\View;

/**
 * {$className}
 *
 * @see http://phpsx.org/doc/design/controller.html
 */
class {$className} extends SchemaApiAbstract
{
	{$services}

	/**
	 * @return PSX\Api\DocumentationInterface
	 */
	public function getDocumentation()
	{
		\$view = new View();
		\$view->setGet(\$this->schemaManager->get('{$namespace}\Schema\GetResponse'));

		return new Documentation\Simple(\$view);
	}

	/**
	 * Returns the GET response
	 *
	 * @param PSX\Api\Version \$version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doGet(Version \$version)
	{
		return array(
			'message' => 'This is the default controller of PSX'
		);
	}

	/**
	 * Returns the POST response
	 *
	 * @param PSX\Data\RecordInterface \$record
	 * @param PSX\Api\Version \$version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doCreate(RecordInterface \$record, Version \$version)
	{
	}

	/**
	 * Returns the PUT response
	 *
	 * @param PSX\Data\RecordInterface \$record
	 * @param PSX\Api\Version \$version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doUpdate(RecordInterface \$record, Version \$version)
	{
	}

	/**
	 * Returns the DELETE response
	 *
	 * @param PSX\Data\RecordInterface \$record
	 * @param PSX\Api\Version \$version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doDelete(RecordInterface \$record, Version \$version)
	{
	}
}

PHP;
	}
}
