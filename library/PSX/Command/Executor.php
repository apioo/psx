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

namespace PSX\Command;

use InvalidArgumentException;
use PSX\CommandInterface;
use PSX\Dispatch\CommandFactoryInterface;
use PSX\Event;
use PSX\Event\Context\CommandContext;
use PSX\Event\CommandExecuteEvent;
use PSX\Event\CommandProcessedEvent;
use PSX\Event\ExceptionThrownEvent;
use PSX\Loader\Location;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Executor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Executor
{
	protected $factory;
	protected $output;
	protected $eventDispatcher;

	protected $aliases = array();

	public function __construct(CommandFactoryInterface $factory, OutputInterface $output, EventDispatcherInterface $eventDispatcher)
	{
		$this->factory         = $factory;
		$this->output          = $output;
		$this->eventDispatcher = $eventDispatcher;
	}

	public function addAlias($alias, $className)
	{
		$this->aliases[$alias] = $className;
	}

	public function getClassName($className)
	{
		return isset($this->aliases[$className]) ? $this->aliases[$className] : $className;
	}

	public function getAliases()
	{
		return $this->aliases;
	}

	public function run(ParameterParserInterface $parser, Location $location = null)
	{
		$location   = $location === null ? new Location() : $location;
		$className  = $this->getClassName($parser->getClassName());

		$command    = $this->factory->getCommand($className, $location);
		$parameters = $command->getParameters();

		$parser->fillParameters($parameters);

		$this->eventDispatcher->dispatch(Event::COMMAND_EXECUTE, new CommandExecuteEvent($command, $parameters));

		try
		{
			$command->onExecute($parameters, $this->output);
		}
		catch(\Exception $e)
		{
			$this->eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new CommandContext($parameters)));

			$class    = isset($this->config['psx_error_command']) ? $this->config['psx_error_command'] : 'PSX\Command\ErrorCommand';
			$location = new Location();
			$location->setParameter(Location::KEY_EXCEPTION, $e);

			$this->factory->getCommand($class, $location)->onExecute(new Parameters(), $this->output);
		}

		$this->eventDispatcher->dispatch(Event::COMMAND_PROCESSED, new CommandProcessedEvent($command, $parameters));

		return $command;
	}
}
