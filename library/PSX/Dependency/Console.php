<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Dependency;

use Doctrine\DBAL\Tools\Console\Command as DBALCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use PSX\Base;
use PSX\Console as PSXCommand;
use PSX\Console\Reader;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Console
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait Console
{
	/**
	 * @return \Symfony\Component\Console\Application
	 */
	public function getConsole()
	{
		$application = new Application('psx', Base::getVersion());
		$application->setHelperSet(new HelperSet($this->appendConsoleHelpers()));

		$this->appendConsoleCommands($application);

		return $application;
	}

	/**
	 * @return \PSX\Console\ReaderInterface
	 */
	public function getConsoleReader()
	{
		return new Reader\Stdin();
	}

	protected function appendConsoleCommands(Application $application)
	{
		$application->add(new PSXCommand\CommandCommand($this->get('executor'), $this->get('console_reader')));
		$application->add(new PSXCommand\ContainerCommand($this));
		$application->add(new PSXCommand\RouteCommand($this->get('routing_parser')));
		$application->add(new PSXCommand\ServeCommand($this->get('config'), $this->get('dispatch'), $this->get('console_reader')));

		$application->add(new PSXCommand\Debug\JsonSchemaCommand());
		$application->add(new PSXCommand\Debug\RamlCommand());

		$application->add(new PSXCommand\Generate\ApiCommand($this));
		$application->add(new PSXCommand\Generate\BootstrapCacheCommand());
		$application->add(new PSXCommand\Generate\CommandCommand($this));
		$application->add(new PSXCommand\Generate\ControllerCommand($this));
		$application->add(new PSXCommand\Generate\SchemaCommand($this->get('connection')));
		$application->add(new PSXCommand\Generate\TableCommand($this->get('connection')));
		$application->add(new PSXCommand\Generate\ViewCommand($this));

		$application->add(new PSXCommand\Schema\JsonSchemaCommand($this->get('config'), $this->get('resource_listing')));
		$application->add(new PSXCommand\Schema\RamlCommand($this->get('config'), $this->get('resource_listing')));
		$application->add(new PSXCommand\Schema\SwaggerCommand($this->get('config'), $this->get('resource_listing')));
		$application->add(new PSXCommand\Schema\WsdlCommand($this->get('config'), $this->get('resource_listing')));
		$application->add(new PSXCommand\Schema\XsdCommand($this->get('config'), $this->get('resource_listing')));

		// symfony commands
		$application->add(new SymfonyCommand\HelpCommand());
		$application->add(new SymfonyCommand\ListCommand());

		// dbal commands
		$application->add(new DBALCommand\ImportCommand());
		$application->add(new DBALCommand\ReservedWordsCommand());
		$application->add(new DBALCommand\RunSqlCommand());
	}

	/**
	 * @return array
	 */
	protected function appendConsoleHelpers()
	{
		return array(
			'db' => new ConnectionHelper($this->get('connection')),
		);
	}
}
