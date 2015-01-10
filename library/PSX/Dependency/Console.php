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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait Console
{
	/**
	 * @return Symfony\Component\Console\Application
	 */
	public function getConsole()
	{
		$application = new Application('psx', Base::getVersion());
		$application->setHelperSet(new HelperSet($this->appendHelpers()));

		$this->appendCommands($application);

		return $application;
	}

	/**
	 * @return PSX\Console\ReaderInterface
	 */
	public function getConsoleReader()
	{
		return new Reader\Stdin();
	}

	protected function appendCommands(Application $application)
	{
		$application->add(new PSXCommand\CommandCommand($this->get('executor'), $this->get('console_reader')));
		$application->add(new PSXCommand\ContainerCommand($this));
		$application->add(new PSXCommand\RouteCommand($this->get('routing_parser')));
		$application->add(new PSXCommand\ServeCommand($this->get('config'), $this->get('dispatch'), $this->get('console_reader')));

		$application->add(new PSXCommand\Generate\ApiCommand($this));
		$application->add(new PSXCommand\Generate\BootstrapCacheCommand());
		$application->add(new PSXCommand\Generate\CommandCommand($this));
		$application->add(new PSXCommand\Generate\ControllerCommand($this));
		$application->add(new PSXCommand\Generate\SchemaCommand($this->get('connection')));
		$application->add(new PSXCommand\Generate\TableCommand($this->get('connection')));
		$application->add(new PSXCommand\Generate\ViewCommand($this));

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
	protected function appendHelpers()
	{
		return array(
			'db' => new ConnectionHelper($this->get('connection')),
		);
	}
}
