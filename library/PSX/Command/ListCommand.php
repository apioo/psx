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

namespace PSX\Command;

use PSX\CommandAbstract;
use PSX\Command\Parameter;
use PSX\Command\Parameters;
use PSX\Command\OutputInterface;
use PSX\Loader\Location;

/**
 * ListCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ListCommand extends CommandAbstract
{
	public function onExecute(Parameters $parameters, OutputInterface $output)
	{
		$aliases    = $this->getExecutor()->getAliases();
		$maxLength  = 0;
		$values     = array();

		foreach($aliases as $alias => $className)
		{
			$command     = $this->getCommandFactory()->getCommand($className, new Location());
			$parameters  = $command->getParameters();
			$description = $parameters->getDescription();
			$length      = strlen($alias);

			if($length > $maxLength)
			{
				$maxLength = $length;
			}

			$values[] = array($alias, $description);
		}

		// print usage
		$output->writeln('Usage:');
		$output->writeln('  psx <command> [<options>]');
		$output->writeln('');

		// print commands
		$output->writeln('Commands:');

		if(count($values) > 0)
		{
			foreach($values as $value)
			{
				$output->writeln('  ' . str_pad($value[0], $maxLength) . '  ' . wordwrap($value[1], 75, "\n" . str_repeat(' ', $maxLength + 4)));
			}
		}
		else
		{
			$output->writeln('  ' . 'No commands available');
		}

		$output->writeln('');
		$output->writeln('See \'psx help -c <command>\' for more information on a specific command.');
		$output->writeln('');
	}

	public function getParameters()
	{
		return $this->getParameterBuilder()
			->setDescription('Lists all available commands')
			->getParameters();
	}
}
