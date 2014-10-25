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

namespace PSX\Dependency;

use PSX\Base;
use PSX\Console\CommandCommand;
use PSX\Console\ContainerCommand;
use PSX\Console\RouteCommand;
use PSX\Console\ServeCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;

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

		$this->appendDefaultCommands($application);

		return $application;
	}

	protected function appendDefaultCommands(Application $application)
	{
		$application->add(new HelpCommand());
		$application->add(new ListCommand());
		$application->add(new CommandCommand($this->get('executor')));
		$application->add(new ContainerCommand($this));
		$application->add(new RouteCommand($this->get('routing_parser')));
		$application->add(new ServeCommand($this->get('config'), $this->get('dispatch')));
	}
}
