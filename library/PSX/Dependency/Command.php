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

use PSX\Command\Executor;
use PSX\Command\Output\Stdout;
use PSX\Console as CliConsole;
use PSX\Dispatch\CommandFactory;

/**
 * Command
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait Command
{
	/**
	 * @return PSX\Dispatch\CommandFactoryInterface
	 */
	public function getCommandFactory()
	{
		return new CommandFactory($this);
	}

	/**
	 * @return PSX\Command\OutputInterface
	 */
	public function getCommandOutput()
	{
		return new Stdout();
	}

	/**
	 * @return PSX\Command\Executor
	 */
	public function getExecutor()
	{
		$executor = new Executor($this->get('command_factory'), $this->get('command_output'));
		$executor->addAlias('help', 'PSX\Command\HelpCommand');
		$executor->addAlias('list', 'PSX\Command\ListCommand');

		return $executor;
	}

	/**
	 * @return PSX\Console
	 */
	public function getConsole()
	{
		return new CliConsole($this->get('executor'), $this->get('config'));
	}
}
